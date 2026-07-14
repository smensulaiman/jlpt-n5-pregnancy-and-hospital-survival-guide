@if(filled($block->body_html))
    <div class="prose">{!! $block->body_html !!}</div>
@endif
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
            @foreach($block->dialogueLines as $line)
                <tr class="spk-{{ $line->speaker_type }}">
                    <td class="spk">{{ $line->speaker_label }}</td>
                    <td class="jp" lang="ja">{!! $line->japanese_ruby ?? e($line->japanese) !!}</td>
                    <td class="ro">{{ $line->romaji }}</td>
                    <td class="en">{{ $line->english }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
