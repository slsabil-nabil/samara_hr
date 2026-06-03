<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Loan;
use App\Models\Payroll;
use App\Models\Penalty;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollService
{
    public function generate(int $month, int $year): Collection
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return Employee::where('status', 'active')->orderBy('name')->get()->map(function (Employee $employee) use ($month, $year, $start, $end) {
            $basic = (float) $employee->basic_salary;
            $allowances = (float) $employee->allowances;
            $base = $basic + $allowances;
            $dailyRate = $base / 26;
            $hourlyRate = $dailyRate / 8;

            $attendanceRows = Attendance::where('employee_id', $employee->id)->whereBetween('date', [$start, $end])->get();
            $overtimeAmount = $attendanceRows->sum(function (Attendance $row) use ($hourlyRate) {
                $multiplier = $row->status === 'day_off' ? 1.50 : 1.25;
                return (float) $row->overtime_hours * $hourlyRate * $multiplier;
            });

            $leaveDeductionDays = LeaveRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('start_date', [$start, $end])
                        ->orWhereBetween('end_date', [$start, $end]);
                })
                ->sum('leave_deduction_days');

            $leaveDeduction = (float) $leaveDeductionDays * $dailyRate;

            $penaltyDeduction = Penalty::where('employee_id', $employee->id)
                ->whereBetween('date', [$start, $end])
                ->sum('deduction_amount');

            $loanDeduction = Loan::where('employee_id', $employee->id)
                ->where('status', 'active')
                ->whereDate('starts_on', '<=', $end)
                ->get()
                ->sum(fn(Loan $loan) => min((float) $loan->monthly_installment, $loan->remaining_amount));

            $gross = $base + $overtimeAmount;
            $net = max(0, $gross - $leaveDeduction - $penaltyDeduction - $loanDeduction);

            return Payroll::updateOrCreate(
                ['employee_id' => $employee->id, 'period_month' => $month, 'period_year' => $year],
                [
                    'basic_salary' => $basic,
                    'allowances' => $allowances,
                    'overtime_amount' => round($overtimeAmount, 3),
                    'leave_deduction' => round($leaveDeduction, 3),
                    'penalty_deduction' => round((float) $penaltyDeduction, 3),
                    'loan_deduction' => round((float) $loanDeduction, 3),
                    'gross_salary' => round($gross, 3),
                    'net_salary' => round($net, 3),
                    'status' => 'draft',
                ]
            );
        });
    }

    public function markPaid(Payroll $payroll): Payroll
    {
        if ($payroll->status === 'paid') {
            return $payroll;
        }

        $remainingDeduction = (float) $payroll->loan_deduction;

        Loan::where('employee_id', $payroll->employee_id)
            ->where('status', 'active')
            ->orderBy('starts_on')
            ->get()
            ->each(function (Loan $loan) use (&$remainingDeduction): void {
                if ($remainingDeduction <= 0) {
                    return;
                }

                $payment = min($remainingDeduction, $loan->remaining_amount);
                $loan->paid_amount = (float) $loan->paid_amount + $payment;
                $loan->status = $loan->paid_amount >= $loan->amount ? 'settled' : 'active';
                $loan->save();
                $remainingDeduction -= $payment;
            });

        $payroll->update(['status' => 'paid', 'paid_at' => now()]);

        return $payroll->refresh();
    }
}
