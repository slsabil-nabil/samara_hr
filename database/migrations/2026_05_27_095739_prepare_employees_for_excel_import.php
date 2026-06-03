<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->string('civil_id')->nullable()->index()->after('name');

            $table->date('hire_date')
                ->nullable()
                ->change();

            $table->decimal('basic_salary', 12, 3)
                ->nullable()
                ->change();

            $table->decimal('allowances', 12, 3)
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('employees')
            ->whereNull('hire_date')
            ->update(['hire_date' => now()->toDateString()]);

        DB::table('employees')
            ->whereNull('basic_salary')
            ->update(['basic_salary' => 0]);

        DB::table('employees')
            ->whereNull('allowances')
            ->update(['allowances' => 0]);

        Schema::table('employees', function (Blueprint $table): void {
            $table->dropIndex(['civil_id']);
            $table->dropColumn('civil_id');

            $table->date('hire_date')
                ->change();

            $table->decimal('basic_salary', 12, 3)
                ->default(0)
                ->change();

            $table->decimal('allowances', 12, 3)
                ->default(0)
                ->change();
        });
    }
};