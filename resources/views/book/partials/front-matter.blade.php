@php
    $roman = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI'];
@endphp

{{-- ============================== COVER ============================== --}}
<main class="sheet sheet--cover sheet--break">
    <div class="cover">
        <div class="hanko" lang="ja" aria-hidden="true">産</div>
        <p class="series">Practical Japanese Series &middot; <span lang="ja"><ruby>実用<rt>じつよう</rt></ruby><ruby>日本語<rt>にほんご</rt></ruby></span></p>
        <h1>Pregnancy &amp;<br>Hospital <em>Survival Guide</em></h1>
        <p class="jp-title">
            <span lang="ja"><ruby>妊娠<rt>にんしん</rt></ruby>・<ruby>出産<rt>しゅっさん</rt></ruby>のための<ruby>病院<rt>びょういん</rt></ruby><ruby>日本語<rt>にほんご</rt></ruby></span>
            <span class="romaji">Ninshin &middot; Shussan no tame no byouin Nihongo</span>
        </p>
        <p class="subtitle">Practical Japanese for Foreign Parents Living in Japan, real hospital conversations for pregnancy, labor, and childbirth.</p>
        <p class="edition">First Edition &middot; Twenty Chapters &middot; Print-Ready A4</p>
    </div>
</main>

{{-- ============================= FOREWORD ============================ --}}
<section class="sheet sheet--break" id="foreword">
    <div class="page-heading">
        <span class="head-seal" lang="ja">序</span>
        <h2>Foreword</h2>
        <span class="head-jp" lang="ja">はじめに</span>
    </div>

    <div class="prose">
        <p>This book was written for one very specific reader: a foreign husband living in Japan whose wife is pregnant, who has roughly JLPT N5 Japanese, and who needs to <strong>communicate right now</strong>, with receptionists, nurses, midwives, doctors, pharmacists, and taxi drivers, during one of the most important events of his life.</p>
        <p>It is not a grammar book. Every chapter is built around <strong>real conversations</strong> in natural, polite です・ます Japanese, exactly as they happen in Japanese maternity hospitals. Where a word above N5 level is unavoidable, such as <span lang="ja"><ruby>陣痛<rt>じんつう</rt></ruby></span> (<em>jintsuu</em>, labor pains), <span lang="ja"><ruby>助産師<rt>じょさんし</rt></ruby></span> (<em>josanshi</em>, midwife), or <span lang="ja"><ruby>母子手帳<rt>ぼしてちょう</rt></ruby></span> (<em>boshi techou</em>, Maternal and Child Health Handbook), it is explained in English the first time it appears, because you <em>will</em> hear it at the hospital whether it is on the JLPT or not.</p>

        <h3>How to use this book</h3>
        <p>Every conversation uses the same four-column table: <strong>Speaker &middot; Japanese &middot; Romaji &middot; English</strong>. Read the Japanese aloud following the romaji, check the English, and repeat until the sentence comes out of your mouth without thinking. Each chapter also gives you a vocabulary table (20+ words), alternative expressions you may hear instead, cultural notes on hospital etiquette, and a set of review phrases to memorize before moving on.</p>

        <h3>The three sentences to learn today</h3>
        <p>If you read nothing else, memorize these. They will carry you through almost any emergency:</p>
    </div>

    <div class="table-scroll">
        <table class="book-table dialogue">
            <colgroup>
                <col class="c-spk">
                <col class="c-jp">
                <col class="c-ro">
                <col class="c-en">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col">Speaker</th>
                    <th scope="col">Japanese</th>
                    <th scope="col">Romaji</th>
                    <th scope="col">English</th>
                </tr>
            </thead>
            <tbody>
                <tr class="spk-husband">
                    <td class="spk">You</td>
                    <td class="jp" lang="ja"><ruby>妻<rt>つま</rt></ruby>は<ruby>妊娠<rt>にんしん</rt></ruby>しています。<ruby>陣痛<rt>じんつう</rt></ruby>が<ruby>始<rt>はじ</rt></ruby>まりました。</td>
                    <td class="ro">Tsuma wa ninshin shite imasu. Jintsuu ga hajimarimashita.</td>
                    <td class="en">My wife is pregnant. Labor has started.</td>
                </tr>
                <tr class="spk-husband">
                    <td class="spk">You</td>
                    <td class="jp" lang="ja"><ruby>破水<rt>はすい</rt></ruby>しました。すぐ<ruby>病院<rt>びょういん</rt></ruby>に<ruby>行<rt>い</rt></ruby>きたいです。</td>
                    <td class="ro">Hasui shimashita. Sugu byouin ni ikitai desu.</td>
                    <td class="en">Her water broke. We want to go to the hospital right away.</td>
                </tr>
                <tr class="spk-husband">
                    <td class="spk">You</td>
                    <td class="jp" lang="ja"><ruby>日本語<rt>にほんご</rt></ruby>があまり<ruby>分<rt>わ</rt></ruby>かりません。ゆっくり<ruby>話<rt>はな</rt></ruby>してください。</td>
                    <td class="ro">Nihongo ga amari wakarimasen. Yukkuri hanashite kudasai.</td>
                    <td class="en">I do not understand much Japanese. Please speak slowly.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <footer class="book-footer">
        <span>JLPT N5 Pregnancy &amp; Hospital Survival Guide</span>
        <span class="page-no">ii</span>
        <span class="jp" lang="ja">はじめに</span>
    </footer>
</section>

{{-- ========================= TABLE OF CONTENTS ======================= --}}
<section class="sheet{{ ($tocBreak ?? false) ? ' sheet--break' : '' }}">
    <div class="page-heading">
        <span class="head-seal" lang="ja">目次</span>
        <h2>Table of Contents</h2>
        <span class="head-jp" lang="ja">もくじ</span>
    </div>

    <ol class="toc-list">
        <li>
            <a href="#foreword">
                <span class="toc-no">&mdash;</span>
                <span class="toc-title">Foreword</span>
                <span class="toc-leader" aria-hidden="true"></span>
                <span class="toc-jp" lang="ja">はじめに</span>
                <span class="toc-page">ii</span>
            </a>
        </li>
    </ol>

    @forelse($parts as $part)
        <div class="toc-part">
            <span class="part-no">Part {{ $roman[$part->number] ?? $part->number }}</span>
            <span class="part-title">{{ preg_replace('/^Part\s+[IVX\d]+\s*[—–-]\s*/iu', '', $part->title_en) }}</span>
            <span class="part-jp" lang="ja">{!! $part->title_jp_ruby ?? e($part->title_jp) !!}</span>
        </div>
        <ol class="toc-list">
            @foreach($part->chapters as $tocChapter)
                <li>
                    <a href="{{ route('book.chapter', $tocChapter) }}">
                        <span class="toc-no">{{ str_pad($tocChapter->number, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="toc-title">{{ $tocChapter->title_en }}</span>
                        <span class="toc-romaji">{{ $tocChapter->title_romaji }}</span>
                        <span class="toc-leader" aria-hidden="true"></span>
                        <span class="toc-jp" lang="ja">{!! $tocChapter->title_jp_ruby ?? e($tocChapter->title_jp) !!}</span>
                        <span class="toc-page">{{ $pages[$tocChapter->number] ?? '' }}</span>
                    </a>
                </li>
            @endforeach
        </ol>
    @empty
        <p class="prose" style="color: var(--ink-soft); font-style: italic;">The table of contents is being prepared&hellip;</p>
    @endforelse

    <footer class="book-footer">
        <span>JLPT N5 Pregnancy &amp; Hospital Survival Guide</span>
        <span class="page-no">iii</span>
        <span class="jp" lang="ja">実用日本語シリーズ</span>
    </footer>
</section>
