@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'autocomplete' => null,
    'required' => false,
    'autofocus' => false,
])

<div class="ui-field">
    <label class="ui-field__label" for="{{ $name }}">{{ $label }}</label>

    <div class="ui-field__control @error($name) is-invalid @enderror">
        <span class="ui-field__icon" aria-hidden="true">
            @if ($type === 'email')
                <svg viewBox="0 0 24 24">
                    <path d="M4.5 5.5h15A2.5 2.5 0 0 1 22 8v8a2.5 2.5 0 0 1-2.5 2.5h-15A2.5 2.5 0 0 1 2 16V8a2.5 2.5 0 0 1 2.5-2.5Zm0 2a.5.5 0 0 0-.5.5v.36l8 5.06 8-5.06V8a.5.5 0 0 0-.5-.5h-15ZM20 10.73l-7.47 4.72a1 1 0 0 1-1.06 0L4 10.73V16a.5.5 0 0 0 .5.5h15a.5.5 0 0 0 .5-.5v-5.27Z"/>
                </svg>
            @elseif ($type === 'password')
                <svg viewBox="0 0 24 24">
                    <path d="M12 2a6 6 0 0 1 6 6v2h.5A2.5 2.5 0 0 1 21 12.5v7a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 19.5v-7A2.5 2.5 0 0 1 5.5 10H6V8a6 6 0 0 1 6-6Zm6.5 10h-13a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5ZM12 14a1.5 1.5 0 0 1 1 2.62V18a1 1 0 1 1-2 0v-1.38A1.5 1.5 0 0 1 12 14Zm0-10a4 4 0 0 0-4 4v2h8V8a4 4 0 0 0-4-4Z"/>
                </svg>
            @else
                <svg viewBox="0 0 24 24">
                    <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4.42 0-8 2.24-8 5v2h16v-2c0-2.76-3.58-5-8-5Z"/>
                </svg>
            @endif
        </span>

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
            @if ($placeholder) placeholder="{{ $placeholder }}" @endif
            @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            @required($required)
            @if ($autofocus) autofocus @endif
            {{ $attributes->class(['ui-field__input']) }}
        >
    </div>

    @error($name)
        <p class="ui-field__error">{{ $message }}</p>
    @enderror
</div>