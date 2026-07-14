<aside class="note{{ $block->type === 'culture_note' ? ' note--culture' : '' }}">
    @if(filled($block->title))
        <p class="note-title">{{ $block->title }}</p>
    @endif
    {!! $block->body_ruby_html ?? $block->body_html !!}
</aside>
