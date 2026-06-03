<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::created(function (Employee $employee): void {
            if (blank($employee->code)) {
                $employee->forceFill([
                    'code' => self::makeCode($employee->id),
                ])->saveQuietly();
            }
        });
    }

    public static function makeCode(int|string $id): string
    {
        return 'EMP-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'basic_salary' => 'decimal:3',
            'allowances' => 'decimal:3',
        ];
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
    public function documents(): HasMany
    {
        return $this->hasMany(OfficialDocument::class);
    }
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class);
    }
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->code ? $this->code . ' - ' : '') . $this->name);
    }
}
