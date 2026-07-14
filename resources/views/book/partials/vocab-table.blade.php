@if(filled($block->body_html))
    <div class="prose">{!! $block->body_html !!}</div>
@endif
<div class="table-scroll">
    <table class="book-table vocab">
        <colgroup>
            <col class="c-jp">
            <col class="c-ro">
            <col class="c-en">
        </colgroup>
        <thead>
            <tr>
                <th scope="col">Japanese</th>
                <th scope="col">Romaji</th>
                <th scope="col">English</th>
            </tr>
        </thead>
        <tbody>
            @foreach($block->vocabWords as $word)
                <tr>
                    <td class="jp" lang="ja">{!! $word->japanese_ruby ?? e($word->japanese) !!}</td>
                    <td class="ro">{{ $word->romaji }}</td>
                    <td class="en">{{ $word->english }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
