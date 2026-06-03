<?php

if (!function_exists('money_kwd')) {
    function money_kwd(float|int|null $amount): string
    {
        return number_format((float) $amount, 3) . ' ' . env('HR_CURRENCY_AR', 'د.ك');
    }
}

if (!function_exists('status_badge')) {
    function status_badge(?string $status): string
    {
        return match ($status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'on_leave' => 'في إجازة',
            'pending' => 'قيد المراجعة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            'paid' => 'مدفوع',
            'draft' => 'مسودة',
            'settled' => 'مغلق',
            'expired' => 'منتهي',
            'expiring_soon' => 'قريب الانتهاء',
            'valid' => 'ساري',
            'present' => 'حاضر',
            'late' => 'متأخر',
            'absent' => 'غائب',
            'day_off' => 'يوم راحة',
            default => $status ?: '-',
        };
    }
    if (!function_exists('days_value')) {
        function days_value(float|int|string|null $value): string
        {
            if ($value === null || $value === '') {
                return '-';
            }

            $number = (float) $value;

            if (floor($number) == $number) {
                return (string) (int) $number;
            }

            return rtrim(rtrim(number_format($number, 2), '0'), '.');
        }
    }
}
