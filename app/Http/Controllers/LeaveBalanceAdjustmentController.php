<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalanceAdjustment;

class LeaveBalanceAdjustmentController extends SimpleResourceController
{
    protected string $model = LeaveBalanceAdjustment::class;
    protected string $routeName = 'leave-balance-adjustments';
    protected string $title = 'تعديل رصيد الإجازات';
    protected string $subtitle = 'تسجيل حركات إضافة أو خصم أو تصحيح رصيد الإجازات للموظفين.';
    protected array $with = ['employee'];
    protected string $orderBy = 'effective_date';
    protected string $orderDirection = 'desc';

    protected array $columns = [
        ['key' => 'employee.name', 'label' => 'الموظف'],
        ['key' => 'type', 'label' => 'نوع الحركة', 'options' => [
            'opening' => 'رصيد افتتاحي',
            'add' => 'إضافة',
            'deduct' => 'خصم',
            'correction' => 'تصحيح',
        ]],
        ['key' => 'days', 'label' => 'عدد الأيام'],
        ['key' => 'effective_date', 'label' => 'تاريخ التأثير', 'format' => 'date'],
        ['key' => 'reason', 'label' => 'السبب'],
    ];

    protected array $fields = [
        ['name' => 'employee_id', 'label' => 'الموظف', 'type' => 'employee_select', 'rules' => ['required', 'exists:employees,id']],
        ['name' => 'type', 'label' => 'نوع الحركة', 'type' => 'select', 'options' => [
            'opening' => 'رصيد افتتاحي',
            'add' => 'إضافة',
            'deduct' => 'خصم',
            'correction' => 'تصحيح',
        ], 'rules' => ['required', 'in:opening,add,deduct,correction']],
        ['name' => 'days', 'label' => 'عدد الأيام', 'type' => 'number', 'step' => '0.5', 'rules' => ['required', 'numeric', 'min:0']],
        ['name' => 'effective_date', 'label' => 'تاريخ التأثير', 'type' => 'date', 'rules' => ['required', 'date']],
        ['name' => 'reason', 'label' => 'السبب', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
        ['name' => 'notes', 'label' => 'ملاحظات', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
    ];
}