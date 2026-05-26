@extends('layouts.app', ['pageTitle' => 'لوحة التحكم'])

@section('content')
@php
    $absenceRate = min(100, max(0, (float) $summary['absence_rate']));
@endphp

<section class="dashboard-welcome">
    <div class="dashboard-welcome__content">
        <span class="dashboard-welcome__eyebrow">لوحة مدير الموارد البشرية</span>

        <h2>مرحباً بك في نظام سمراء اليمن</h2>

        <p>
            تابع الموظفين والرواتب والوثائق والحضور من لوحة واحدة منظمة
            تعتمد على البيانات الفعلية المسجلة في النظام.
        </p>
    </div>

    <div class="dashboard-welcome__actions">
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            إضافة موظف جديد
        </a>

        <a href="{{ route('reports') }}" class="btn btn-light">
            عرض التقارير
        </a>
    </div>
</section>

<section class="stats-grid">
    <x-stat-card
        title="إجمالي الموظفين"
        :value="$summary['employees']"
        hint="جميع الملفات المسجلة"
        icon="employees"
    />

    <x-stat-card
        title="الموظفون النشطون"
        :value="$summary['active_employees']"
        hint="حالة وظيفية نشطة"
        icon="active"
    />

    <x-stat-card
        title="صافي رواتب الشهر"
        :value="money_kwd($summary['monthly_payroll'])"
        hint="بالدينار الكويتي"
        icon="payroll"
    />

    <x-stat-card
        title="القروض المفتوحة"
        :value="money_kwd($summary['open_loans'])"
        hint="إجمالي الرصيد المتبقي"
        icon="loans"
    />

    <x-stat-card
        title="الإجازات المعلقة"
        :value="$summary['pending_leaves']"
        hint="طلبات تحتاج مراجعة"
        icon="leaves"
    />

    <x-stat-card
        title="وثائق قرب الانتهاء"
        :value="$summary['expiring_documents']"
        hint="خلال 45 يوماً"
        icon="documents"
    />
</section>

@if($summary['employees'] == 0)
    <section class="setup-card">
        <div class="setup-card__icon">س</div>

        <div class="setup-card__content">
            <h3>ابدأ بإضافة بيانات الموظفين</h3>

            <p>
                لا توجد سجلات موظفين حالياً. بعد إضافة الموظفين يمكنك إدارة الحضور،
                الإجازات، الوثائق، القروض، الجزاءات ومسيرات الرواتب.
            </p>
        </div>

        <a class="btn btn-primary" href="{{ route('employees.create') }}">
            إضافة أول موظف
        </a>
    </section>
@endif

<div class="dashboard-overview">
    <section class="panel quick-access-panel">
        <div class="panel__head">
            <div>
                <span class="panel__eyebrow">الوحدات الأساسية</span>
                <h3>الوصول السريع</h3>
            </div>
        </div>

        <div class="quick-access-grid">
            <a class="quick-access-card" href="{{ route('employees.index') }}">
                <span class="quick-access-card__icon">01</span>
                <strong>الموظفون</strong>
                <small>الملفات والبيانات الوظيفية</small>
            </a>

            <a class="quick-access-card" href="{{ route('payrolls.index') }}">
                <span class="quick-access-card__icon">02</span>
                <strong>الرواتب</strong>
                <small>المسيرات والاستحقاقات</small>
            </a>

            <a class="quick-access-card" href="{{ route('attendances.index') }}">
                <span class="quick-access-card__icon">03</span>
                <strong>الحضور</strong>
                <small>الدوام والغياب والتأخير</small>
            </a>

            <a class="quick-access-card" href="{{ route('documents.index') }}">
                <span class="quick-access-card__icon">04</span>
                <strong>الوثائق</strong>
                <small>الإقامات والجوازات</small>
            </a>
        </div>
    </section>

    <section class="panel attendance-performance">
        <div class="panel__head">
            <div>
                <span class="panel__eyebrow">الحضور</span>
                <h3>معدل الغياب الشهري</h3>
            </div>
        </div>

        <div class="attendance-performance__body">
            <div class="progress-ring" style="--progress: {{ $absenceRate }};">
                <div class="progress-ring__center">
                    <strong>{{ $summary['absence_rate'] }}%</strong>
                    <small>نسبة الغياب</small>
                </div>
            </div>

            <div class="performance-details">
                <div>
                    <span>حالات الغياب</span>
                    <strong>{{ $summary['monthly_absences'] }}</strong>
                </div>

                <div>
                    <span>الموظفون النشطون</span>
                    <strong>{{ $summary['active_employees'] }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="panel documents-panel">
        <div class="panel__head">
            <div>
                <span class="panel__eyebrow">التنبيهات</span>
                <h3>الوثائق الرسمية</h3>
            </div>

            <a href="{{ route('documents.index') }}">عرض الكل</a>
        </div>

        <div class="mini-list">
            @forelse($expiringDocuments as $document)
                <div class="mini-row mini-row--warning">
                    <div>
                        <strong>{{ $document->employee?->name }}</strong>
                        <small>
                            {{ status_badge($document->type) }}
                            -
                            {{ optional($document->expires_on)->format('Y-m-d') }}
                        </small>
                    </div>

                    <span class="badge">{{ status_badge($document->status) }}</span>
                </div>
            @empty
                <div class="empty-card">
                    لا توجد وثائق قريبة الانتهاء.
                </div>
            @endforelse
        </div>
    </section>
</div>

<div class="dashboard-lists">
    <section class="panel">
        <div class="panel__head">
            <div>
                <span class="panel__eyebrow">الرواتب</span>
                <h3>آخر المسيرات</h3>
            </div>

            <a href="{{ route('payrolls.index') }}">عرض الكل</a>
        </div>

        <div class="mini-list">
            @forelse($latestPayrolls as $payroll)
                <div class="mini-row">
                    <div>
                        <strong>{{ $payroll->employee?->name }}</strong>
                        <small>{{ $payroll->period_month }}/{{ $payroll->period_year }}</small>
                    </div>

                    <span class="money-text">{{ money_kwd($payroll->net_salary) }}</span>
                </div>
            @empty
                <div class="empty-card">
                    لم يتم توليد رواتب بعد.
                </div>
            @endforelse
        </div>
    </section>

    <section class="panel panel--attendance">
        <div class="panel__head">
            <div>
                <span class="panel__eyebrow">سجل الدوام</span>
                <h3>آخر الحضور والانصراف</h3>
            </div>

            <a href="{{ route('attendances.index') }}">عرض الكل</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>التاريخ</th>
                        <th>الدخول</th>
                        <th>الخروج</th>
                        <th>الحالة</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($recentAttendance as $row)
                        <tr>
                            <td>{{ $row->employee?->name }}</td>
                            <td>{{ optional($row->date)->format('Y-m-d') }}</td>
                            <td>{{ $row->check_in ?: '-' }}</td>
                            <td>{{ $row->check_out ?: '-' }}</td>
                            <td>
                                <span class="badge">{{ status_badge($row->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                لا توجد بيانات حضور بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection