@props([
    'title',
    'value',
    'hint',
    'icon' => 'employees',
])

@php
    $icons = [
        'employees' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Zm0 2c-4.14 0-7.5 2.31-7.5 5.15V21h15v-1.85C19.5 16.31 16.14 14 12 14Z"/></svg>',
        'active' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.7 7.7-5.5 6a1 1 0 0 1-1.47.03l-2.4-2.4a1 1 0 1 1 1.41-1.41l1.66 1.66 4.83-5.27a1 1 0 1 1 1.47 1.36Z"/></svg>',
        'payroll' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Zm2 2h14V7H5v2Zm10.5 4a1.5 1.5 0 1 0 0 3h2a1.5 1.5 0 0 0 0-3h-2Z"/></svg>',
        'loans' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16a2 2 0 0 1 2 2v2H2V7a2 2 0 0 1 2-2Zm-2 6h20v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6Zm4 4a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2H6Z"/></svg>',
        'leaves' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 1-1Zm13 8H4v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-9Z"/></svg>',
        'documents' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm7 1.8V8h4.2L13 3.8ZM8 12a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8Zm0 4a1 1 0 1 0 0 2h5a1 1 0 1 0 0-2H8Z"/></svg>',
    ];

    $iconSvg = $icons[$icon] ?? $icons['employees'];
@endphp

<article class="stat-card">
    <div class="stat-card__head">
        <span class="stat-card__title">{{ $title }}</span>
        <span class="stat-card__icon">{!! $iconSvg !!}</span>
    </div>

    <strong class="stat-card__value">{{ $value }}</strong>

    <small class="stat-card__hint">{{ $hint }}</small>
</article>