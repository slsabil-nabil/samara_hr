@props([
    'name',
    'value' => null,
    'placeholder' => 'اختر التاريخ',
])

@php
    $dateValue = '';

    if ($value instanceof \Carbon\CarbonInterface) {
        $dateValue = $value->format('Y-m-d');
    } elseif (! empty($value)) {
        try {
            $dateValue = \Illuminate\Support\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            $dateValue = '';
        }
    }
@endphp

<div class="date-picker" data-date-picker data-initial-value="{{ $dateValue }}">
    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ $dateValue }}"
        data-date-picker-input
    >

    <button
        type="button"
        class="date-picker__button"
        data-date-picker-button
        aria-expanded="false"
    >
        <span data-date-picker-label>{{ $dateValue ?: $placeholder }}</span>

        <svg class="date-picker__icon" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7 2v3M17 2v3M4 9h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/>
        </svg>
    </button>

    <div class="date-picker__menu" data-date-picker-menu hidden>
        <div class="date-picker__head">
            <button type="button" class="date-picker__nav" data-date-picker-prev>‹</button>
            <strong data-date-picker-title></strong>
            <button type="button" class="date-picker__nav" data-date-picker-next>›</button>
        </div>

        <div class="date-picker__weekdays">
            <span>ح</span>
            <span>ن</span>
            <span>ث</span>
            <span>ر</span>
            <span>خ</span>
            <span>ج</span>
            <span>س</span>
        </div>

        <div class="date-picker__days" data-date-picker-days></div>

        <div class="date-picker__footer">
            <button type="button" class="date-picker__today" data-date-picker-today>اليوم</button>
            <button type="button" class="date-picker__clear" data-date-picker-clear>مسح</button>
        </div>
    </div>
</div>