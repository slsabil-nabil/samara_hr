@extends('layouts.app', ['title' => 'تسجيل الدخول | سمراء اليمن'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth/login.css') }}">
@endpush

@section('content')
<section class="samaraa-login" aria-label="صفحة تسجيل الدخول">
    <div class="samaraa-login__shell">
        <aside class="samaraa-login__art" aria-hidden="true">
            <div class="samaraa-login__art-lines">
                <span class="samaraa-login__art-line samaraa-login__art-line--top"></span>
                <span class="samaraa-login__art-line samaraa-login__art-line--bottom"></span>
                <span class="samaraa-login__art-line samaraa-login__art-line--gold"></span>
            </div>

            <div class="samaraa-login__art-brand">
                <span class="samaraa-login__art-brand-mark">س</span>
                <div>
                    <strong>سمراء اليمن</strong>
                    <small>نظام الموارد البشرية</small>
                </div>
            </div>

            <div class="samaraa-login__art-footer">
                <span></span>
                <small>إدارة موظفين أكثر تنظيماً</small>
            </div>
        </aside>

        <div class="samaraa-login__panel">
            <div class="samaraa-login__form-content">
                <header class="samaraa-login__header">
                    <div class="samaraa-login__avatar" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 12.3a4.45 4.45 0 1 0 0-8.9 4.45 4.45 0 0 0 0 8.9Z"/>
                            <path d="M4.35 20.3c.48-3.76 3.72-6.25 7.65-6.25s7.17 2.49 7.65 6.25"/>
                        </svg>
                    </div>

                    <p class="samaraa-login__brand-name">سمراء اليمن</p>

                    <h1 class="samaraa-login__title">تسجيل الدخول</h1>

                    <p class="samaraa-login__description">
                        نظام إدارة الموارد البشرية
                    </p>
                </header>

                @if ($errors->any() && ! $errors->has('email') && ! $errors->has('password'))
                    <div class="samaraa-login__alert" role="alert">
                        تعذر تسجيل الدخول. راجعي بيانات الحساب ثم حاولي مرة أخرى.
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    class="samaraa-login__form"
                    autocomplete="on"
                >
                    @csrf

                    <x-ui.input
                        name="email"
                        label="البريد الإلكتروني"
                        type="email"
                        placeholder="البريد الإلكتروني"
                        autocomplete="username"
                        :required="true"
                        :autofocus="true"
                    />

                    <x-ui.input
                        name="password"
                        label="كلمة المرور"
                        type="password"
                        placeholder="كلمة المرور"
                        autocomplete="current-password"
                        :required="true"
                    />

                    <div class="samaraa-login__secondary">
                        <label class="samaraa-login__remember">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                @checked(old('remember'))
                            >
                            <span>تذكرني</span>
                        </label>

                        <span class="samaraa-login__role">
                            مدير الموارد البشرية
                        </span>
                    </div>

                    <div class="samaraa-login__actions">
                        <span class="samaraa-login__secure">
                            دخول آمن للنظام
                        </span>

                        <x-ui.button type="submit">
                            دخول
                        </x-ui.button>
                    </div>
                </form>

                <footer class="samaraa-login__footer">
                    <span></span>
                    <p>منصة سمراء اليمن الإدارية</p>
                    <span></span>
                </footer>
            </div>
        </div>
    </div>
</section>
@endsection