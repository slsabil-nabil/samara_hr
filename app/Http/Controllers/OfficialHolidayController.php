<?php

namespace App\Http\Controllers;

use App\Models\OfficialHoliday;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OfficialHolidayController extends SimpleResourceController
{
    protected string $model = OfficialHoliday::class;
    protected string $routeName = 'official-holidays';
    protected string $title = 'الإجازات الرسمية';
    protected string $subtitle = 'إدارة الإجازات الرسمية التي لا تخصم من رصيد الإجازات السنوية.';
    protected string $orderBy = 'starts_on';
    protected string $orderDirection = 'desc';

    protected array $columns = [
        ['key' => 'name', 'label' => 'اسم الإجازة'],
        ['key' => 'starts_on', 'label' => 'تاريخ البداية', 'format' => 'date'],
        ['key' => 'ends_on', 'label' => 'تاريخ النهاية', 'format' => 'date'],
        ['key' => 'days', 'label' => 'عدد الأيام'],
        ['key' => 'year', 'label' => 'السنة'],
        ['key' => 'is_recurring', 'label' => 'تتكرر سنوياً', 'options' => ['0' => 'لا', '1' => 'نعم']],
    ];

    protected array $fields = [
        ['name' => 'name', 'label' => 'اسم الإجازة', 'type' => 'text', 'rules' => ['required', 'string', 'max:255']],
        ['name' => 'starts_on', 'label' => 'تاريخ البداية', 'type' => 'date', 'rules' => ['required', 'date']],
        ['name' => 'ends_on', 'label' => 'تاريخ النهاية', 'type' => 'date', 'rules' => ['required', 'date', 'after_or_equal:starts_on']],
        ['name' => 'is_recurring', 'label' => 'تتكرر سنوياً', 'type' => 'select', 'options' => ['0' => 'لا', '1' => 'نعم'], 'rules' => ['required', 'boolean']],
        ['name' => 'notes', 'label' => 'ملاحظات', 'type' => 'textarea', 'rules' => ['nullable', 'string']],
    ];

    protected function mutateData(array $data, ?Model $record): array
    {
        $startsOn = Carbon::parse($data['starts_on']);
        $endsOn = Carbon::parse($data['ends_on']);

        $data['days'] = $startsOn->diffInDays($endsOn) + 1;
        $data['year'] = (int) $startsOn->year;
        $data['is_recurring'] = (bool) $data['is_recurring'];

        return $data;
    }
}