@switch($block->type)
    @case('paragraph')
        <div class="prose">{!! $block->body_ruby_html ?? $block->body_html !!}</div>
        @break

    @case('scene')
        <p class="scene">{!! $block->body_ruby_html ?? $block->body_html !!}</p>
        @break

    @case('dialogue')
        @include('book.partials.dialogue-table', ['block' => $block])
        @break

    @case('vocab')
        @include('book.partials.vocab-table', ['block' => $block])
        @break

    @case('note')
    @case('culture_note')
        @include('book.partials.note', ['block' => $block])
        @break
@endswitch
