@extends('layouts.app', ['pageTitle' => $title])

@section('content')
    <section class="page-head">
        <div>
            <h2>{{ $title }}</h2>

            @if ($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
        </div>

        <div class="page-head__actions">
            @if ($routeName === 'employees')
                <a class="btn btn-light" href="{{ route('employees.import.form') }}">
                    استيراد من Excel
                </a>
            @endif

            <a class="btn btn-primary" href="{{ route($routeName . '.create') }}">
                + إضافة جديد
            </a>
        </div>
    </section>

    <section class="panel">
        <form class="toolbar" method="GET">
            <input type="search" name="q" value="{{ $search }}" placeholder="بحث سريع...">

            <button class="btn btn-light" type="submit">بحث</button>

            @if ($search)
                <a class="btn btn-ghost" href="{{ route($routeName . '.index') }}">مسح</a>
            @endif
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach

                        <th>الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($records as $record)
                        <tr>
                            @foreach ($columns as $column)
                                @php
                                    $value = data_get($record, $column['key']);
                                    $format = $column['format'] ?? 'text';
                                @endphp

                                <td>
                                    @if ($format === 'money')
                                        {{ $value !== null && $value !== '' ? money_kwd($value) : '-' }}
                                    @elseif($format === 'date')
                                        {{ $value ? \Illuminate\Support\Carbon::parse($value)->format('Y-m-d') : '-' }}
                                    @elseif($format === 'status')
                                        <span class="badge">{{ status_badge($value) }}</span>
                                    @else
                                        {{ $value !== null && $value !== '' ? $value : '-' }}
                                    @endif
                                </td>
                            @endforeach

                            <td class="actions-cell">
                                <a class="btn btn-sm btn-light" href="{{ route($routeName . '.edit', $record) }}">
                                    تعديل
                                </a>

                                <form method="POST" action="{{ route($routeName . '.destroy', $record) }}"
                                    onsubmit="return confirm('هل تريد حذف السجل؟')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger" type="submit">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + 1 }}" class="empty">
                                لا توجد سجلات بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $records])
    </section>
@endsection
