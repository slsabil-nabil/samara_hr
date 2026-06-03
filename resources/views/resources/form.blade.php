@extends('layouts.app', ['pageTitle' => $title])

@section('content')
    <section class="page-head">
        <div>
            <h2>{{ $record->exists ? 'تعديل' : 'إضافة' }} - {{ $title }}</h2>
            @if ($subtitle)
                <p>{{ $subtitle }}</p>
            @endif
        </div>
        <a class="btn btn-light" href="{{ route($routeName . '.index') }}">رجوع</a>
    </section>
    @if ($routeName === 'leaves' && $record->exists)
        <section class="panel leave-calculation-panel">
            @php
                $leaveTypeLabels = [
                    'annual' => 'إجازة سنوية',
                    'unpaid' => 'إجازة بدون أجر',
                    'sick' => 'إجازة مرضية',
                    'hajj' => 'إجازة حج',
                    'maternity' => 'إجازة وضع',
                    'other' => 'إجازة أخرى',
                ];
            @endphp

            <div class="panel__head">
                <div>
                    <span class="panel__eyebrow">تفاصيل الحساب</span>
                    <h3>{{ $leaveTypeLabels[$record->type] ?? 'الإجازة' }}</h3>
                </div>

                <span class="badge">{{ status_badge($record->status) }}</span>
            </div>

            <div class="leave-summary-grid">
                <div class="leave-summary-card">
                    <span>إجمالي الأيام</span>
                    <strong>{{ days_value($record->days) }}</strong>
                </div>

                <div class="leave-summary-card">
                    <span>أيام رسمية داخل الفترة</span>
                    <strong>{{ days_value($record->official_holiday_days) }}</strong>
                </div>

                <div class="leave-summary-card">
                    <span>المخصوم من الرصيد</span>
                    <strong>{{ days_value($record->deducted_days) }}</strong>
                </div>

                <div class="leave-summary-card">
                    <span>أيام خصم الراتب</span>
                    <strong>{{ days_value($record->leave_deduction_days) }}</strong>
                </div>

                <div class="leave-summary-card">
                    <span>الرصيد قبل الإجازة</span>
                    <strong>{{ days_value($record->balance_before) }}</strong>
                </div>

                <div class="leave-summary-card">
                    <span>الرصيد بعد الإجازة</span>
                    <strong>{{ days_value($record->balance_after) }}</strong>
                </div>
            </div>

            @if ($record->type === 'sick')
                <div class="leave-sick-breakdown">
                    <strong>تفصيل الإجازة المرضية حسب الشرائح</strong>

                    <div class="leave-summary-grid">
                        <div class="leave-summary-card">
                            <span>أجر كامل</span>
                            <strong>{{ days_value($record->sick_full_pay_days) }}</strong>
                        </div>

                        <div class="leave-summary-card">
                            <span>ثلاثة أرباع الأجر</span>
                            <strong>{{ days_value($record->sick_three_quarter_pay_days) }}</strong>
                        </div>

                        <div class="leave-summary-card">
                            <span>نصف الأجر</span>
                            <strong>{{ days_value($record->sick_half_pay_days) }}</strong>
                        </div>

                        <div class="leave-summary-card">
                            <span>ربع الأجر</span>
                            <strong>{{ days_value($record->sick_quarter_pay_days) }}</strong>
                        </div>

                        <div class="leave-summary-card">
                            <span>بدون أجر</span>
                            <strong>{{ days_value($record->sick_unpaid_days) }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            @if ($record->type === 'annual')
                <p class="leave-calculation-note">
                    الإجازات الرسمية الواقعة داخل فترة الإجازة السنوية لا يتم خصمها من رصيد الموظف.
                </p>
            @endif
        </section>
    @endif
    <section class="panel">
        <form method="POST"
            action="{{ $record->exists ? route($routeName . '.update', $record) : route($routeName . '.store') }}"
            class="form-grid">
            @csrf
            @if ($record->exists)
                @method('PUT')
            @endif

            @foreach ($fields as $field)
                @php
                    $name = $field['name'];
                    $type = $field['type'] ?? 'text';
                    $value = old($name, data_get($record, $name));
                    if ($value instanceof \Carbon\CarbonInterface) {
                        $value = $value->format($type === 'time' ? 'H:i' : 'Y-m-d');
                    }
                    if (is_bool($value)) {
                        $value = $value ? '1' : '0';
                    }
                @endphp
                <label class="{{ $type === 'textarea' ? 'full' : '' }}">
                    <span>{{ $field['label'] }}</span>
                    @if ($type === 'textarea')
                        <textarea name="{{ $name }}" rows="4">{{ $value }}</textarea>
                    @elseif($type === 'select')
                        <x-form.custom-select :name="$name" :options="$field['options']" :value="$value" />
                    @elseif($type === 'employee_select')
                        <x-form.custom-select :name="$name" :options="$employees
                            ->mapWithKeys(fn($employee) => [$employee->id => $employee->display_name])
                            ->all()" :value="$value"
                            placeholder="اختر الموظف" :searchable="true" :allow-empty="true" />
                    @elseif($type === 'date')
                        <x-form.date-picker :name="$name" :value="$value" />
                    @else
                        <input type="{{ $type === 'money' ? 'number' : $type }}" name="{{ $name }}"
                            value="{{ $value }}"
                            step="{{ $field['step'] ?? ($type === 'money' ? '0.001' : '1') }}">
                    @endif
                    @error($name)
                        <em>{{ $message }}</em>
                    @enderror
                </label>
            @endforeach

            <div class="form-actions full">
                <button class="btn btn-primary" type="submit">حفظ البيانات</button>
                <a class="btn btn-light" href="{{ route($routeName . '.index') }}">إلغاء</a>
            </div>
        </form>
    </section>
@endsection
