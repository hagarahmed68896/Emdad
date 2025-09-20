@extends('layouts.admin')

@section('page_title', 'إدارة النصوص')

@section('content')
<div class="p-6 overflow-y-auto" 
     x-data="{ selectedTexts: [], selectAll: false }" 
     x-init="$watch('selectAll', value => { 
        selectedTexts = value ? @js($texts->pluck('id')) : [] 
     })">

    <p class="text-[32px] font-bold mb-4">إدارة النصوص</p>

    {{-- Top Controls --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        
        {{-- Show Search/Add/Filter only if no rows selected --}}
        <div x-show="selectedTexts.length === 0" x-cloak class="flex flex-col md:flex-row justify-between items-center w-full gap-4">
            
            {{-- Search --}}
            <form action="{{ route('admin.site_texts.index') }}" method="GET" class="w-full md:w-auto">
                <div class="relative w-full sm:w-[300px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث عن نص"
                           class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500">

                    {{-- Search Icon --}}
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] hover:bg-green-800 rounded-l-xl">
                        بحث
                    </button>
                </div>
            </form>

            {{-- Add + Filter --}}
            <div class="flex flex-row items-center gap-3">
                {{-- <a href="{{ route('admin.site_texts.create') }}"
                   class="bg-[#185D31] hover:bg-green-800 text-white py-2 px-4 rounded-xl flex items-center transition duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                         stroke="currentColor" class="w-5 h-5 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    إضافة نص جديد
                </a> --}}

                <form action="{{ route('admin.site_texts.index') }}" method="GET">
                    <select name="page_name" onchange="this.form.submit()"
                        class="pl-2 pr-4 py-2 border border-gray-300 rounded-xl 
                               focus:outline-none focus:ring-green-500 focus:border-green-500">
                        <option value="">كل الصفحات</option>
                        @foreach($pages as $page)
                            <option value="{{ $page }}" {{ request('page_name') == $page ? 'selected' : '' }}>
                                {{ $page }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- When rows selected, show count --}}
        <div x-show="selectedTexts.length > 0" class="w-full text-right">
            <p class="text-lg font-semibold text-[#185D31]">
                تم تحديد <span x-text="selectedTexts.length"></span> صف
            </p>
        </div>
    </div>


    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 mt-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-center">
                        <input type="checkbox" x-model="selectAll" class="h-4 w-4 text-[#185D31] border-[#185D31] rounded">
                    </th>
                    <th class="px-6 py-3 text-center">#</th>
                    <th class="px-6 py-3 text-right">النص بالعربية</th>
                    <th class="px-6 py-3 text-right">النص بالإنجليزية</th>
                    <th class="px-6 py-3 text-right">الصفحة</th>
                    <th class="px-6 py-3 text-center">الإجراءات</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($texts as $text)
                    <tr>
                        <td class="px-4 py-4 text-center">
                            <input type="checkbox" x-model="selectedTexts" value="{{ $text->id }}" class="h-4 w-4 text-[#185D31] border-[#185D31] rounded">
                        </td>
                        <td class="px-6 py-4 text-center">{{ $loop->iteration + $texts->firstItem() - 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $text->value_ar }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $text->value_en }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $text->page_name }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.site_texts.edit', $text->id) }}" class="text-[#185D31]  mx-2">
                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                               </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            لا توجد نصوص لعرضها.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $texts->links('pagination::tailwind') }}
    </div>
</div>
@endsection
