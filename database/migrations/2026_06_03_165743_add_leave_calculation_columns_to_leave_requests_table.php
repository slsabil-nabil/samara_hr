<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table): void {
            $table->unsignedInteger('official_holiday_days')->default(0);
            $table->decimal('deducted_days', 8, 2)->default(0);
            $table->decimal('paid_days', 8, 2)->default(0);
            $table->decimal('unpaid_days', 8, 2)->default(0);

            $table->decimal('balance_before', 8, 2)->nullable();
            $table->decimal('balance_after', 8, 2)->nullable();

            $table->decimal('sick_full_pay_days', 8, 2)->default(0);
            $table->decimal('sick_three_quarter_pay_days', 8, 2)->default(0);
            $table->decimal('sick_half_pay_days', 8, 2)->default(0);
            $table->decimal('sick_quarter_pay_days', 8, 2)->default(0);
            $table->decimal('sick_unpaid_days', 8, 2)->default(0);

            $table->decimal('leave_deduction_days', 8, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table): void {
            $table->dropColumn([
                'official_holiday_days',
                'deducted_days',
                'paid_days',
                'unpaid_days',
                'balance_before',
                'balance_after',
                'sick_full_pay_days',
                'sick_three_quarter_pay_days',
                'sick_half_pay_days',
                'sick_quarter_pay_days',
                'sick_unpaid_days',
                'leave_deduction_days',
            ]);
        });
    }
};