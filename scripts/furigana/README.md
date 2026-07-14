# Furigana pipeline

Regenerates the `*_ruby` columns (furigana `<ruby>` markup) from the plain
Japanese columns. Re-run this after re-importing or editing book content.

```sh
cd scripts/furigana
php export_ja.php                                  # dumps ja_export.json
NODE_PATH=../../node_modules node furigana.cjs ja_export.json ja_ruby.json
php import_ruby.php                                # writes *_ruby columns
```

- Vocab words shaped `漢字（かな）` use their curated reading directly (handled
  in `import_ruby.php`); everything else is annotated by kuroshiro/kuromoji.
- Medical terms kuromoji misreads are pinned in `OVERRIDES` in `furigana.cjs`
  (悪露, 会陰, 母子手帳, …). Add new terms there if you spot a wrong reading.
- The absolute project paths at the top of the PHP scripts assume this machine;
  adjust if the project moves.
