@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'searchable' => false,
    'allowEmpty' => false,
])

@php
    $preparedOptions = [];

    foreach ($options as $optionValue => $optionLabel) {
        $preparedOptions[(string) $optionValue] = (string) $optionLabel;
    }

    $selectedValue = (string) ($value ?? '');
    $hasSelectedValue = $selectedValue !== '' && array_key_exists($selectedValue, $preparedOptions);

    if ($hasSelectedValue) {
        $selectedLabel = $preparedOptions[$selectedValue];
    } elseif ($allowEmpty) {
        $selectedValue = '';
        $selectedLabel = $placeholder ?? 'اختر';
    } else {
        $selectedValue = (string) (array_key_first($preparedOptions) ?? '');
        $selectedLabel = $preparedOptions[$selectedValue] ?? ($placeholder ?? 'اختر');
    }
@endphp

<div class="custom-select" data-custom-select>
    <input
        type="hidden"
        name="{{ $name }}"
        value="{{ $selectedValue }}"
        data-custom-select-input
    >

    <div
        class="custom-select__button"
        role="button"
        tabindex="0"
        data-custom-select-button
        aria-expanded="false"
    >
        <span data-custom-select-label>{{ $selectedLabel }}</span>

        <svg class="custom-select__arrow" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M7 10l5 5 5-5" />
        </svg>
    </div>

    <div class="custom-select__menu" data-custom-select-menu hidden>
        @if ($searchable)
            <div class="custom-select__search-wrap">
                <input
                    type="search"
                    class="custom-select__search"
                    placeholder="بحث..."
                    autocomplete="off"
                    data-custom-select-search
                >
            </div>
        @endif

        <div class="custom-select__options">
            @foreach ($preparedOptions as $optionValue => $optionLabel)
                <div
                    class="custom-select__option {{ $selectedValue === $optionValue ? 'is-selected' : '' }}"
                    role="option"
                    tabindex="0"
                    data-custom-select-option
                    data-value="{{ $optionValue }}"
                    data-label="{{ $optionLabel }}"
                >
                    <span>{{ $optionLabel }}</span>
                    <b aria-hidden="true">✓</b>
                </div>
            @endforeach
        </div>
    </div>
</div>