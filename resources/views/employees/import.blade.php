@extends('layouts.app', ['pageTitle' => 'استيراد الموظفين'])

@section('content')
<section class="page-head">
    <div>
        <h2>استيراد الموظفين من Excel</h2>
        <p>
            ارفعي ملف الموظفين وسيتم إدراج الأعمدة المطلوبة فقط مع تجاهل بقية الأعمدة.
        </p>
    </div>

    <a class="btn btn-light" href="{{ route('employees.index') }}">
        رجوع إلى الموظفين
    </a>
</section>

<div class="import-layout">
    <section class="panel import-panel">
        <div class="import-panel__head">
            <span class="import-panel__badge">Excel</span>

            <div>
                <h3>اختيار ملف الموظفين</h3>
                <p>الصيغ المقبولة: XLSX أو XLS أو CSV</p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('employees.import.store') }}"
            enctype="multipart/form-data"
            class="import-form"
        >
            @csrf

            <label class="import-dropzone">
                <input
                    type="file"
                    name="file"
                    accept=".xlsx,.xls,.csv"
                    required
                >

                <span class="import-dropzone__icon">XLS</span>
                <strong>اضغطي لاختيار ملف Excel</strong>
                <small>سيتم تجاهل الأعمدة غير المطلوبة تلقائياً</small>
            </label>

            @error('file')
                <p class="import-error">{{ $message }}</p>
            @enderror

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">
                    استيراد الموظفين
                </button>

                <a class="btn btn-light" href="{{ route('employees.index') }}">
                    إلغاء
                </a>
            </div>
        </form>
    </section>

    <section class="panel import-rules">
        <div class="panel__head">
            <div>
                <span class="panel__eyebrow">الأعمدة المعتمدة</span>
                <h3>ما الذي سيتم استيراده؟</h3>
            </div>
        </div>

        <div class="import-fields">
            <span>الاسم</span>
            <span>القسم</span>
            <span>المهمة</span>
            <span>الراتب الأساسي</span>
            <span>بدلات</span>
            <span>الرقم المدني</span>
            <span>تاريخ التعيين</span>
        </div>

        <div class="import-note">
            <strong>ملاحظات الاستيراد</strong>

            <p>
                الصف بدون اسم لن يتم إدراجه. القيم الفارغة في بقية الأعمدة ستبقى
                فارغة. الصف الذي يحمل رقماً مدنياً موجوداً مسبقاً سيتم تجاهله.
            </p>
        </div>
    </section>
</div>
@endsection