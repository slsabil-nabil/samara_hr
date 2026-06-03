<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class EmployeeController extends SimpleResourceController
{
    protected string $model = Employee::class;
    protected string $routeName = 'employees';
    protected string $title = 'إدارة الموظفين';
    protected string $subtitle = 'قاعدة بيانات مركزية للموظفين مع بيانات الراتب والحالة الوظيفية.';
    protected string $orderBy = 'name';
    protected string $orderDirection = 'asc';

    protected array $columns = [
        ['key' => 'code', 'label' => 'الكود'],
        ['key' => 'name', 'label' => 'اسم الموظف'],
        ['key' => 'civil_id', 'label' => 'الرقم المدني'],
        ['key' => 'hire_date', 'label' => 'تاريخ التعيين', 'format' => 'date'],
        ['key' => 'job_title', 'label' => 'المهمة'],
        ['key' => 'department', 'label' => 'القسم'],
        ['key' => 'basic_salary', 'label' => 'الراتب الأساسي', 'format' => 'money'],
        ['key' => 'allowances', 'label' => 'البدلات', 'format' => 'money'],
        ['key' => 'status', 'label' => 'الحالة', 'format' => 'status'],
    ];

    protected array $fields = [
        ['name' => 'code', 'label' => 'كود الموظف', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:50']],
        ['name' => 'name', 'label' => 'اسم الموظف', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
        ['name' => 'civil_id', 'label' => 'الرقم المدني', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:50']],
        ['name' => 'job_title', 'label' => 'المهمة', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
        ['name' => 'department', 'label' => 'القسم', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
        ['name' => 'phone', 'label' => 'الهاتف', 'type' => 'text', 'rules' => ['nullable', 'string', 'max:50']],
        ['name' => 'email', 'label' => 'البريد', 'type' => 'email', 'rules' => ['nullable', 'email', 'max:255']],
        ['name' => 'hire_date', 'label' => 'تاريخ التعيين', 'type' => 'date', 'rules' => ['nullable', 'date']],
        ['name' => 'basic_salary', 'label' => 'الراتب الأساسي', 'type' => 'money', 'rules' => ['nullable', 'numeric', 'min:0']],
        ['name' => 'allowances', 'label' => 'البدلات', 'type' => 'money', 'rules' => ['nullable', 'numeric', 'min:0']],
        ['name' => 'status', 'label' => 'الحالة', 'type' => 'select', 'options' => ['active' => 'نشط', 'on_leave' => 'في إجازة', 'inactive' => 'غير نشط'], 'rules' => ['required', 'in:active,on_leave,inactive']],
        ['name' => 'notes', 'label' => 'ملاحظات', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
    ];

    public function importForm(): View
    {
        return view('employees.import');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ], [], [
            'file' => 'ملف Excel',
        ]);

        $filePath = $request->file('file')->getRealPath();

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
        } catch (Throwable) {
            throw ValidationException::withMessages([
                'file' => 'تعذر قراءة ملف Excel. تأكدي أن الملف صالح ثم حاولي مرة أخرى.',
            ]);
        }

        $columns = $this->resolveColumns($sheet);

        $created = 0;
        $skippedWithoutName = 0;
        $skippedDuplicateCivilId = 0;

        DB::transaction(function () use ($sheet, $columns, &$created, &$skippedWithoutName, &$skippedDuplicateCivilId): void {
            for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
                $name = $this->nullableText(
                    $sheet->getCell($columns['name'] . $row)->getFormattedValue()
                );

                if ($name === null) {
                    $skippedWithoutName++;
                    continue;
                }

                $civilId = $this->nullableCivilId(
                    $sheet->getCell($columns['civil_id'] . $row)->getFormattedValue()
                );

                if (
                    $civilId !== null
                    && Employee::query()->where('civil_id', $civilId)->exists()
                ) {
                    $skippedDuplicateCivilId++;
                    continue;
                }

                Employee::query()->create([
                    'name' => $name,
                    'civil_id' => $civilId,
                    'department' => $this->nullableText(
                        $sheet->getCell($columns['department'] . $row)->getFormattedValue()
                    ),
                    'job_title' => $this->nullableText(
                        $sheet->getCell($columns['job_title'] . $row)->getFormattedValue()
                    ),
                    'basic_salary' => $this->nullableDecimal(
                        $sheet->getCell($columns['basic_salary'] . $row)->getValue()
                    ),
                    'allowances' => $this->nullableDecimal(
                        $sheet->getCell($columns['allowances'] . $row)->getValue()
                    ),
                    'hire_date' => $this->nullableDate(
                        $sheet->getCell($columns['hire_date'] . $row)->getValue(),
                        $sheet->getCell($columns['hire_date'] . $row)->getFormattedValue()
                    ),
                    'status' => 'active',
                ]);

                $created++;
            }
        });

        $message = "تم استيراد {$created} موظف بنجاح.";

        if ($skippedWithoutName > 0) {
            $message .= " تم تجاهل {$skippedWithoutName} صف بدون اسم.";
        }

        if ($skippedDuplicateCivilId > 0) {
            $message .= " تم تجاهل {$skippedDuplicateCivilId} صف برقم مدني موجود مسبقاً.";
        }

        return redirect()
            ->route('employees.index')
            ->with('success', $message);
    }

    private function resolveColumns(Worksheet $sheet): array
    {
        $aliases = [
            'name' => ['الاسم'],
            'department' => ['القسم'],
            'job_title' => ['المهمة', 'المسمي', 'المسمى'],
            'basic_salary' => ['الراتبالاساسي'],
            'allowances' => ['بدلات', 'البدلات'],
            'civil_id' => ['الرقمالمدني', 'الرقمالمدين'],
            'hire_date' => ['تاريخالتعيين', 'تاريخالالتحاق'],
        ];

        $labels = [
            'name' => 'الاسم',
            'department' => 'القسم',
            'job_title' => 'المهمة',
            'basic_salary' => 'الراتب الأساسي',
            'allowances' => 'بدلات',
            'civil_id' => 'الرقم المدني',
            'hire_date' => 'تاريخ التعيين',
        ];

        $columns = [];
        $lastColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        for ($index = 1; $index <= $lastColumn; $index++) {
            $columnLetter = Coordinate::stringFromColumnIndex($index);
            $header = $this->normalizeHeader(
                $sheet->getCell($columnLetter . '1')->getFormattedValue()
            );

            foreach ($aliases as $field => $fieldAliases) {
                $normalizedAliases = array_map(
                    fn(string $alias): string => $this->normalizeHeader($alias),
                    $fieldAliases
                );

                if (in_array($header, $normalizedAliases, true)) {
                    $columns[$field] = $columnLetter;
                    break;
                }
            }
        }

        $missing = [];

        foreach ($labels as $field => $label) {
            if (!isset($columns[$field])) {
                $missing[] = $label;
            }
        }

        if ($missing !== []) {
            throw ValidationException::withMessages([
                'file' => 'الملف لا يحتوي على الأعمدة المطلوبة: ' . implode('، ', $missing) . '.',
            ]);
        }

        return $columns;
    }

    private function normalizeHeader(string $header): string
    {
        $header = trim($header);
        $header = str_replace(['أ', 'إ', 'آ', 'ى'], ['ا', 'ا', 'ا', 'ي'], $header);

        return preg_replace('/[\s\x{0640}_\-]+/u', '', $header) ?? '';
    }

    private function nullableText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function nullableCivilId(mixed $value): ?string
    {
        $value = $this->nullableText($value);

        if ($value === null) {
            return null;
        }

        return preg_replace('/\s+/u', '', $value) ?: null;
    }

    private function nullableDecimal(mixed $value): ?float
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = str_replace([',', '٬', ' '], '', (string) $value);
        $value = strtr($value, [
            '٠' => '0',
            '١' => '1',
            '٢' => '2',
            '٣' => '3',
            '٤' => '4',
            '٥' => '5',
            '٦' => '6',
            '٧' => '7',
            '٨' => '8',
            '٩' => '9',
        ]);

        return is_numeric($value) ? round((float) $value, 3) : null;
    }

    private function nullableDate(mixed $rawValue, mixed $formattedValue): ?string
    {
        if (
            ($rawValue === null || trim((string) $rawValue) === '')
            && ($formattedValue === null || trim((string) $formattedValue) === '')
        ) {
            return null;
        }

        if (is_numeric($rawValue)) {
            try {
                return Carbon::instance(
                    SpreadsheetDate::excelToDateTimeObject((float) $rawValue)
                )->toDateString();
            } catch (Throwable) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $formattedValue)->toDateString();
        } catch (Throwable) {
            return null;
        }
    }
}