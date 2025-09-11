{{-- resources/views/settlements/index.blade.php --}}
@extends('layouts.admin')
@section('page_title', 'التسويات')

@section('content')
<div class="p-6 space-y-6">

    {{-- الكروت العلوية --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h2 class="text-xl font-bold">{{ $totalSettlements }}</h2>
            <p class="text-gray-600">إجمالي التسويات المالية</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h2 class="text-xl font-bold">{{ $totalPending }}</h2>
            <p class="text-gray-600">إجمالي المبالغ المعلقة</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6 text-center">
            <h2 class="text-xl font-bold">{{ $totalTransferred }}</h2>
            <p class="text-gray-600">إجمالي المبالغ المحولة</p>
        </div>
    </div>

    {{-- أزرار --}}
    <div class="flex justify-between items-center">
        <form method="GET" class="flex gap-2">
            <select name="status" class="border rounded p-2">
                <option value="">الكل</option>
                <option value="معلقة">معلقة</option>
                <option value="محوّلة">محوّلة</option>
            </select>
            <button class="bg-green-600 text-white px-4 py-2 rounded">فلترة</button>
        </form>

        <div class="flex gap-2">
            <a href="{{ route('settlements.download') }}" class="bg-gray-600 text-white px-4 py-2 rounded">تحميل</a>
            <a href="{{route('settlements.create')}}" data-modal-target="addModal" class="bg-green-600 text-white px-4 py-2 rounded">+ إضافة تسوية</a>
        </div>
    </div>

    {{-- جدول --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-right border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">رقم التسوية</th>
                    <th class="p-3">اسم المورد</th>
                    <th class="p-3">رقم الطلب</th>
                    <th class="p-3">المبلغ</th>
                    <th class="p-3">الحالة</th>
                    <th class="p-3">التاريخ</th>
                    <th class="p-3">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($settlements as $s)
                <tr class="border-b">
                    <td class="p-3">{{ $s->id }}</td>
                    <td class="p-3">#تسوية-{{ $s->id }}</td>
                    <td class="p-3">{{ $s->supplier->name ?? '-' }}</td>
                    <td class="p-3">#طلب-{{ $s->request_number }}</td>
                    <td class="p-3">{{ $s->amount }} رس</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-white {{ $s->status == 'محوّلة' ? 'bg-green-600' : 'bg-gray-500' }}">
                            {{ $s->status }}
                        </span>
                    </td>
                    <td class="p-3">{{ $s->settlement_date }}</td>
                    <td class="p-3 flex gap-2">
                        <form action="{{ route('settlements.destroy', $s) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="bg-red-500 text-white px-3 py-1 rounded">🗑️ حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4">
            {{ $settlements->links() }}
        </div>
    </div>
</div>
@endsection
