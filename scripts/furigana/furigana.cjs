/**
 * Add furigana (<ruby>) to every kanji in the exported Japanese text.
 * Usage: node furigana.cjs ja_export.json ja_ruby.json
 * Medical/maternity terms kuromoji tends to misread are pinned in OVERRIDES.
 */
const fs = require('fs');
const path = require('path');
const Kuroshiro = require('kuroshiro').default || require('kuroshiro');
const KuromojiAnalyzer = require('kuroshiro-analyzer-kuromoji');

// term -> reading, applied whole-word; longest first so 会陰切開 wins over 会陰
const OVERRIDES = [
  ['陣痛促進剤', 'じんつうそくしんざい'],
  ['妊婦健診', 'にんぷけんしん'],
  ['会陰切開', 'えいんせっかい'],
  ['母子手帳', 'ぼしてちょう'],
  ['問診票', 'もんしんひょう'],
  ['診察券', 'しんさつけん'],
  ['保険証', 'ほけんしょう'],
  ['分娩台', 'ぶんべんだい'],
  ['分娩室', 'ぶんべんしつ'],
  ['助産師', 'じょさんし'],
  ['初産婦', 'しょさんぷ'],
  ['経産婦', 'けいさんぷ'],
  ['へその緒', 'へそのお'],
  ['産褥', 'さんじょく'],
  ['分娩', 'ぶんべん'],
  ['会陰', 'えいん'],
  ['悪露', 'おろ'],
  ['初乳', 'しょにゅう'],
  ['沐浴', 'もくよく'],
  ['黄疸', 'おうだん'],
  ['臍帯', 'さいたい'],
  ['陣痛', 'じんつう'],
  ['破水', 'はすい'],
  ['内診', 'ないしん'],
];

const rubyFor = (word, reading) =>
  `<ruby>${word}<rp>（</rp><rt>${reading}</rt><rp>）</rp></ruby>`;

async function main() {
  const [, , inFile, outFile] = process.argv;
  const items = JSON.parse(fs.readFileSync(inFile, 'utf8'));

  const kuroshiro = new Kuroshiro();
  await kuroshiro.init(new KuromojiAnalyzer());

  async function convertText(text) {
    // Pin override terms behind ASCII placeholders kuroshiro leaves alone.
    const slots = [];
    let masked = text;
    for (const [term, reading] of OVERRIDES) {
      while (masked.includes(term)) {
        const token = `[[R${slots.length}]]`;
        slots.push(rubyFor(term, reading));
        masked = masked.replace(term, token);
      }
    }
    let out = await kuroshiro.convert(masked, { mode: 'furigana', to: 'hiragana' });
    slots.forEach((ruby, i) => {
      out = out.replace(`[[R${i}]]`, ruby);
    });
    return out;
  }

  async function convertHtml(html) {
    // Only annotate text directly inside lang="ja" elements.
    const re = /(<[^>]*lang=["']ja["'][^>]*>)([^<]+)(<)/g;
    const parts = [];
    let last = 0;
    let m;
    while ((m = re.exec(html)) !== null) {
      parts.push(html.slice(last, m.index) + m[1]);
      parts.push(await convertText(m[2]));
      last = m.index + m[1].length + m[2].length;
    }
    parts.push(html.slice(last));
    return parts.join('');
  }

  for (const item of items) {
    item.ruby = item.kind === 'html' ? await convertHtml(item.text) : await convertText(item.text);
    delete item.text;
  }

  fs.writeFileSync(outFile, JSON.stringify(items), 'utf8');
  console.log(`${items.length} items converted.`);
}

main().catch((e) => { console.error(e); process.exit(1); });
