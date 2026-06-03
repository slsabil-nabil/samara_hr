<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveBalanceAdjustment;
use App\Models\LeaveRequest;
use App\Models\OfficialHoliday;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LeavePolicyService
{
    private const ANNUAL_DAYS_PER_MONTH = 2.5;
    private const MAX_ANNUAL_BALANCE = 60;

    public function calculate(array $data, ?LeaveRequest $record = null): array
    {
        $employee = Employee::query()->findOrFail($data['employee_id']);

        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->startOfDay();
        $type = (string) $data['type'];
        $status = (string) ($data['status'] ?? 'pending');

        $totalDays = $startDate->diffInDays($endDate) + 1;

        $calculated = [
            'days' => $totalDays,
            'official_holiday_days' => 0,
            'deducted_days' => 0,
            'paid_days' => 0,
            'unpaid_days' => 0,
            'balance_before' => null,
            'balance_after' => null,
            'sick_full_pay_days' => 0,
            'sick_three_quarter_pay_days' => 0,
            'sick_half_pay_days' => 0,
            'sick_quarter_pay_days' => 0,
            'sick_unpaid_days' => 0,
            'leave_deduction_days' => 0,
        ];

        if ($type === 'annual') {
            return array_merge(
                $data,
                $this->calculateAnnualLeave($employee, $startDate, $endDate, $totalDays, $status, $record),
            );
        }

        if ($type === 'sick') {
            return array_merge(
                $data,
                $this->calculateSickLeave($employee, $startDate, $totalDays, $record),
            );
        }

        if ($type === 'unpaid') {
            $calculated['unpaid_days'] = $totalDays;
            $calculated['leave_deduction_days'] = $totalDays;
            $data['paid'] = false;

            return array_merge($data, $calculated);
        }

        $isPaid = (bool) ($data['paid'] ?? true);

        $calculated['paid_days'] = $isPaid ? $totalDays : 0;
        $calculated['unpaid_days'] = $isPaid ? 0 : $totalDays;
        $calculated['leave_deduction_days'] = $isPaid ? 0 : $totalDays;
        $data['paid'] = $isPaid;

        return array_merge($data, $calculated);
    }

    private function calculateAnnualLeave(
        Employee $employee,
        Carbon $startDate,
        Carbon $endDate,
        int $totalDays,
        string $status,
        ?LeaveRequest $record = null
    ): array {
        $officialHolidayDays = $this->officialHolidayDaysBetween($startDate, $endDate);
        $deductedDays = max(0, $totalDays - $officialHolidayDays);

        $balanceBefore = $this->annualBalance($employee, $startDate, $record?->id);

        $paidDays = min($deductedDays, $balanceBefore);
        $unpaidDays = max(0, $deductedDays - $balanceBefore);

        return [
            'days' => $totalDays,
            'official_holiday_days' => $officialHolidayDays,
            'deducted_days' => $deductedDays,
            'paid_days' => round($paidDays, 2),
            'unpaid_days' => round($unpaidDays, 2),
            'balance_before' => round($balanceBefore, 2),
            'balance_after' => $status === 'approved'
                ? round(max(0, $balanceBefore - $deductedDays), 2)
                : round($balanceBefore, 2),
            'sick_full_pay_days' => 0,
            'sick_three_quarter_pay_days' => 0,
            'sick_half_pay_days' => 0,
            'sick_quarter_pay_days' => 0,
            'sick_unpaid_days' => 0,
            'leave_deduction_days' => round($unpaidDays, 2),
            'paid' => $unpaidDays <= 0,
        ];
    }

    private function calculateSickLeave(
        Employee $employee,
        Carbon $startDate,
        int $totalDays,
        ?LeaveRequest $record = null
    ): array {
        $usedSickDays = (int) LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->where('type', 'sick')
            ->where('status', 'approved')
            ->whereYear('start_date', $startDate->year)
            ->when($record?->id, fn ($query, $id) => $query->whereKeyNot($id))
            ->sum('days');

        $bands = $this->sickBands($totalDays, $usedSickDays);

        $deductionDays =
            ($bands['three_quarter'] * 0.25)
            + ($bands['half'] * 0.50)
            + ($bands['quarter'] * 0.75)
            + $bands['unpaid'];

        return [
            'days' => $totalDays,
            'official_holiday_days' => 0,
            'deducted_days' => 0,
            'paid_days' => round(
                $bands['full'] + $bands['three_quarter'] + $bands['half'] + $bands['quarter'],
                2
            ),
            'unpaid_days' => round($bands['unpaid'], 2),
            'balance_before' => null,
            'balance_after' => null,
            'sick_full_pay_days' => round($bands['full'], 2),
            'sick_three_quarter_pay_days' => round($bands['three_quarter'], 2),
            'sick_half_pay_days' => round($bands['half'], 2),
            'sick_quarter_pay_days' => round($bands['quarter'], 2),
            'sick_unpaid_days' => round($bands['unpaid'], 2),
            'leave_deduction_days' => round($deductionDays, 2),
            'paid' => $deductionDays <= 0,
        ];
    }

    private function sickBands(int $requestedDays, int $alreadyUsedDays): array
    {
        $bands = [
            'full' => 0,
            'three_quarter' => 0,
            'half' => 0,
            'quarter' => 0,
            'unpaid' => 0,
        ];

        for ($day = 1; $day <= $requestedDays; $day++) {
            $annualSickDayNumber = $alreadyUsedDays + $day;

            if ($annualSickDayNumber <= 15) {
                $bands['full']++;
            } elseif ($annualSickDayNumber <= 25) {
                $bands['three_quarter']++;
            } elseif ($annualSickDayNumber <= 35) {
                $bands['half']++;
            } elseif ($annualSickDayNumber <= 45) {
                $bands['quarter']++;
            } else {
                $bands['unpaid']++;
            }
        }

        return $bands;
    }

    private function annualBalance(Employee $employee, Carbon $asOfDate, ?int $exceptLeaveId = null): float
    {
        $hireDate = Carbon::parse($employee->hire_date)->startOfMonth();
        $asOfMonth = $asOfDate->copy()->startOfMonth();

        $workedMonths = $hireDate->greaterThan($asOfMonth)
            ? 0
            : $hireDate->diffInMonths($asOfMonth) + 1;

        $earnedBalance = min(
            self::MAX_ANNUAL_BALANCE,
            $workedMonths * self::ANNUAL_DAYS_PER_MONTH
        );

        $adjustments = LeaveBalanceAdjustment::query()
            ->where('employee_id', $employee->id)
            ->whereDate('effective_date', '<=', $asOfDate)
            ->get()
            ->sum(function (LeaveBalanceAdjustment $adjustment): float {
                $days = (float) $adjustment->days;

                return $adjustment->type === 'deduct' ? -$days : $days;
            });

        $usedAnnualDays = LeaveRequest::query()
            ->where('employee_id', $employee->id)
            ->where('type', 'annual')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $asOfDate)
            ->when($exceptLeaveId, fn ($query, $id) => $query->whereKeyNot($id))
            ->sum('deducted_days');

        return max(0, (float) $earnedBalance + (float) $adjustments - (float) $usedAnnualDays);
    }

    private function officialHolidayDaysBetween(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if ($this->isOfficialHoliday($date)) {
                $days++;
            }
        }

        return $days;
    }

    private function isOfficialHoliday(Carbon $date): bool
    {
        return OfficialHoliday::query()
            ->where(function ($query) use ($date): void {
                $query->where(function ($query) use ($date): void {
                    $query->where('is_recurring', false)
                        ->whereDate('starts_on', '<=', $date)
                        ->whereDate('ends_on', '>=', $date);
                })->orWhere('is_recurring', true);
            })
            ->get()
            ->contains(function (OfficialHoliday $holiday) use ($date): bool {
                if (! $holiday->is_recurring) {
                    return true;
                }

                $target = (int) $date->format('md');
                $start = (int) $holiday->starts_on->format('md');
                $end = (int) $holiday->ends_on->format('md');

                if ($start <= $end) {
                    return $target >= $start && $target <= $end;
                }

                return $target >= $start || $target <= $end;
            });
    }
}