<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? ($pageTitle ?? 'نظام الموارد البشرية | سمراء اليمن') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/ui.css') }}">

    @stack('styles')
</head>

<body class="{{ auth()->check() ? 'is-authenticated' : 'is-guest' }}">
    <div class="app-shell {{ auth()->check() ? '' : 'guest-shell' }}" data-shell>
        @auth
            <aside class="sidebar" data-sidebar>
                <div class="sidebar__brand">
                    <span class="sidebar__brand-mark" aria-hidden="true">س</span>

                    <div class="sidebar__brand-text">
                        <strong>سمراء اليمن</strong>
                        <small>نظام الموارد البشرية</small>
                    </div>
                </div>

                @php
                    $links = [
                        [
                            'route' => 'dashboard',
                            'label' => 'لوحة التحكم',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h6V4H4v9Zm0 7h6v-4H4v4Zm10 0h6v-9h-6v9Zm0-12h6V4h-6v4Z"/></svg>',
                        ],
                        [
                            'route' => 'employees.index',
                            'label' => 'الموظفون',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11c1.66 0 3-1.57 3-3.5S17.66 4 16 4s-3 1.57-3 3.5S14.34 11 16 11ZM8 11c1.66 0 3-1.57 3-3.5S9.66 4 8 4 5 5.57 5 7.5 6.34 11 8 11Zm8 2c-2.33 0-7 1.22-7 3.65V19h14v-2.35C23 14.22 18.33 13 16 13Zm-8 0c-.29 0-.62.02-.97.05C4.72 13.29 1 14.42 1 16.65V19h6v-2.35c0-1.4.78-2.55 2.06-3.47A8.8 8.8 0 0 0 8 13Z"/></svg>',
                        ],
                        [
                            'route' => 'payrolls.index',
                            'label' => 'الرواتب',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7.5A2.5 2.5 0 0 1 5.5 5h13A2.5 2.5 0 0 1 21 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-13A2.5 2.5 0 0 1 3 16.5v-9Zm2 1.75h14V7.5a.5.5 0 0 0-.5-.5h-13a.5.5 0 0 0-.5.5v1.75ZM16.5 14a1.5 1.5 0 1 0 0 3h1.25a1.5 1.5 0 0 0 0-3H16.5Z"/></svg>',
                        ],
                        [
                            'route' => 'leaves.index',
                            'label' => 'الإجازات',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h1V3a1 1 0 0 1 1-1Zm13 8H4v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-9Zm-5.3 2.3a1 1 0 0 1 0 1.4l-3.2 3.2a1 1 0 0 1-1.4 0l-1.8-1.8a1 1 0 1 1 1.4-1.4l1.1 1.1 2.5-2.5a1 1 0 0 1 1.4 0Z"/></svg>',
                        ],
                        [
                            'route' => 'loans.index',
                            'label' => 'القروض والسلف',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16a2 2 0 0 1 2 2v2H2V7a2 2 0 0 1 2-2Zm-2 6h20v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6Zm4 4a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2H6Z"/></svg>',
                        ],
                        [
                            'route' => 'documents.index',
                            'label' => 'الوثائق الرسمية',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 2h8l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm7 1.8V8h4.2L13 3.8ZM8 12a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8Zm0 4a1 1 0 1 0 0 2h5a1 1 0 1 0 0-2H8Z"/></svg>',
                        ],
                        [
                            'route' => 'penalties.index',
                            'label' => 'الجزاءات',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2 2 21h20L12 2Zm0 6a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0V9a1 1 0 0 1 1-1Zm0 11a1.25 1.25 0 1 1 0-2.5A1.25 1.25 0 0 1 12 19Z"/></svg>',
                        ],
                        [
                            'route' => 'attendances.index',
                            'label' => 'الحضور والانصراف',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 5v4.58l3.2 1.9a1 1 0 0 1-1.02 1.72l-3.7-2.2A1 1 0 0 1 11 12.15V7a1 1 0 1 1 2 0Z"/></svg>',
                        ],
                        [
                            'route' => 'trainings.index',
                            'label' => 'التدريب والتطوير',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 1 8l11 5 9-4.09V16h2V8L12 3Zm-6 9.2V16c0 2.2 3.58 4 6 4s6-1.8 6-4v-3.8l-6 2.73-6-2.73Z"/></svg>',
                        ],
                        [
                            'route' => 'reports',
                            'label' => 'التقارير',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 19h14a1 1 0 1 1 0 2H5a2 2 0 0 1-2-2V5a1 1 0 0 1 2 0v14Zm3-2a1 1 0 0 1-1-1v-5a1 1 0 1 1 2 0v5a1 1 0 0 1-1 1Zm4 0a1 1 0 0 1-1-1V7a1 1 0 1 1 2 0v9a1 1 0 0 1-1 1Zm4 0a1 1 0 0 1-1-1v-3a1 1 0 1 1 2 0v3a1 1 0 0 1-1 1Zm4 0a1 1 0 0 1-1-1V9a1 1 0 1 1 2 0v7a1 1 0 0 1-1 1Z"/></svg>',
                        ],
                        [
                            'route' => 'audit.index',
                            'label' => 'سجل التدقيق',
                            'icon' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 2h6a2 2 0 0 1 2 2h1a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1a2 2 0 0 1 2-2Zm0 4h6V4H9v2Zm-1 6a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8Zm0 4a1 1 0 1 0 0 2h5a1 1 0 1 0 0-2H8Z"/></svg>',
                        ],
                    ];
                @endphp

                <span class="sidebar__section-title">القائمة الرئيسية</span>

                <nav class="sidebar__nav" aria-label="القائمة الرئيسية">
                    @foreach ($links as $link)
                        @php
                            $route = $link['route'];
                            $activePattern = str_replace('.index', '.*', $route);
                            $isActive = request()->routeIs($activePattern) || request()->routeIs($route);
                        @endphp

                        <a
                            href="{{ route($route) }}"
                            class="sidebar__link {{ $isActive ? 'active' : '' }}"
                        >
                            <span class="sidebar__icon">{!! $link['icon'] !!}</span>
                            <b>{{ $link['label'] }}</b>
                        </a>
                    @endforeach
                </nav>

                <div class="sidebar__account">
                    <span class="sidebar__avatar" aria-hidden="true">م</span>

                    <div>
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>مدير الموارد البشرية</small>
                    </div>
                </div>
            </aside>
        @endauth

        <main class="main-content">
            @auth
                <header class="topbar">
                    <button
                        class="icon-btn only-mobile"
                        data-sidebar-toggle
                        type="button"
                        aria-label="فتح القائمة"
                    >
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </button>

                    <div class="topbar__title">
                        <p>نظام الموارد البشرية</p>
                        <h1>{{ $pageTitle ?? 'لوحة التحكم' }}</h1>
                    </div>

                    <div class="topbar__actions">
                        <span class="currency-chip">KWD</span>

                        <div class="user-chip">
                            <span class="user-chip__mark">م</span>
                            <div>
                                <strong>{{ auth()->user()->name }}</strong>
                                <small>مدير الموارد البشرية</small>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button class="logout-btn" type="submit" aria-label="تسجيل الخروج">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M10 17l-5-5 5-5M5 12h11M15 4h3a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-3"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </header>
            @endauth

            @auth
                @if (session('success'))
                    <div class="alert success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert danger">
                        <strong>راجع البيانات:</strong>

                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endauth

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>

</html>