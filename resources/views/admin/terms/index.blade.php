@extends('layouts.admin')
@section('page_title', 'الشروط و الأحكام')

@section('content')
<div class="p-6 bg-gray-50 h-screen overflow-y-auto flex flex-col">

    <h2 class="text-[32px] font-bold mb-6 text-gray-800">الشروط و الأحكام</h2>

    <!-- Switch buttons -->
    <div class="flex gap-4 mb-6">
        <a href="{{ route('admin.terms.index', ['user_type' => 'customer']) }}"
           class="px-4 py-2 rounded-lg font-semibold 
           {{ $userType == 'customer' ? 'bg-[#185D31] text-white' : 'bg-gray-200 text-gray-700' }}">
            العملاء
        </a>
        <a href="{{ route('admin.terms.index', ['user_type' => 'supplier']) }}"
           class="px-4 py-2 rounded-lg font-semibold 
           {{ $userType == 'supplier' ? 'bg-[#185D31] text-white' : 'bg-gray-200 text-gray-700' }}">
            الموردين
        </a>
    </div>



    <!-- Active Terms -->
    <div class="bg-white p-4">
        <div class="flex justify-between">
        <h3 class="font-bold text-lg mb-4">الشروط النشطة</h3>
         <!-- Add button -->
    <a href="{{ route('admin.terms.create', ['user_type' => $userType]) }}" 
       class="bg-[#185D31] text-white px-4 py-2 rounded-md mb-4 inline-flex">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
</svg>

       إضافة شروط
    </a>
    </div>
        @forelse($active as $term)
            <div class="bg-white p-4 rounded-md shadow mb-4">
                                <div class="flex justify-between">
                                    <div>
                <h4 class="font-semibold">{{ $term->title }}</h4>
                                <p class="text-sm text-gray-600">تم التحديث في {{ $term->updated_at->translatedFormat('j F Y') }}</p>

                                    </div>
              <div class="flex gap-2 mt-2">
                    <a href="{{ route('admin.terms.edit', $term->id) }}" class="bg-[#185D31] p-2 text-white rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-5">
  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
</svg>

                    </a>
             <!-- Delete Button (Trigger Modal) -->
<button type="button" 
        onclick="openDeleteModal('{{ $term->id }}')" 
        class="text-red-600 bg-gray-100 p-2 rounded-lg">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
    </svg>
</button>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
        <h2 class="text-lg font-bold mb-4 text-gray-800">تأكيد الحذف</h2>
        <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد حذف هذا الشرط؟ لا يمكن التراجع بعد الحذف.</p>

        <div class="flex justify-end gap-3">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">
                إلغاء
            </button>

            <!-- Form will be injected dynamically -->
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                    نعم، احذف
                </button>
            </form>
        </div>
    </div>
</div>

<!-- JS to control modal -->
<script>
    function openDeleteModal(termId) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        form.action = `/admin/terms/${termId}`;
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>

                </div>
                                </div>
                <p class="mt-2  mb-6 text-gray-700">{{ Str::limit($term->body, 150) }}</p>

                <span class="bg-green-100 text-green-700 px-4 py-2 mt-4 text-[14px] rounded-full">نشط</span>
  
            </div>
        @empty
            <p class="text-gray-500">لا توجد شروط نشطة.</p>
        @endforelse
    </div>

    <!-- Previous Versions -->
    <div class="mt-10">
        <h3 class="font-bold text-lg mb-4">الإصدارات السابقة</h3>
        @forelse($previous as $term)
            <div class="bg-white p-4 rounded-md shadow mb-4">
                <div class="flex justify-between">
                    <div>
                <h4 class="font-semibold">{{ $term->title }}</h4>
                                <p class="text-sm text-gray-600">تم التحديث في {{ $term->updated_at->translatedFormat('j F Y') }}</p>

                    </div>
   <a href="{{ route('admin.terms.edit', $term->id) }}" 
                   class="bg-[#185D31] text-white px-3 py-2 rounded-lg mt-2 inline-block">
                    عرض النسخة
                </a>
                </div>
                <p class="mt-2 text-gray-700">{{ Str::limit($term->body, 150) }}</p>
             
            </div>
        @empty
            <p class="text-gray-500">لا توجد نسخ سابقة.</p>
        @endforelse
    </div>
</div>
@endsection
