<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'paid' => 'boolean',
            'days' => 'integer',
            'official_holiday_days' => 'integer',
            'deducted_days' => 'decimal:2',
            'paid_days' => 'decimal:2',
            'unpaid_days' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'sick_full_pay_days' => 'decimal:2',
            'sick_three_quarter_pay_days' => 'decimal:2',
            'sick_half_pay_days' => 'decimal:2',
            'sick_quarter_pay_days' => 'decimal:2',
            'sick_unpaid_days' => 'decimal:2',
            'leave_deduction_days' => 'decimal:2',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
