@php
    $slPages = [
        'shopware-seo'             => 'Shopware SEO',
        'meta-tags-optimieren'     => 'Meta-Tags optimieren',
        'produktbeschreibungen-seo'=> 'Produktbeschreibungen SEO',
        'alt-text-generator'       => 'Alt-Text Generator',
        'seo-audit'                => 'SEO Audit',
        'seo-fuer-agenturen'       => 'SEO für Agenturen',
    ];
@endphp
<div class="sl-related">
    <h3>Weitere Themen</h3>
    <div class="sl-related-links">
        @foreach($slPages as $slug => $label)
            @if($slug !== ($currentSlug ?? ''))
                <a href="{{ route('landing.' . $slug) }}">{{ $label }}</a>
            @endif
        @endforeach
    </div>
</div>
