@extends('layouts.admin')

@section('page_title', __('messages.edit_document'))

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
    <div class="bg-white rounded-xl shadow p-6 mx-auto">
        <h2 class="text-2xl mb-4 font-bold">{{ __('messages.edit_document_data') }}</h2>

        <form action="{{ route('admin.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- اسم الشركة -->
            <label class="block mb-2 font-semibold text-[#212121]">
                {{ __('messages.company_name') }}
            </label>
            <select name="supplier_id" class="w-full border rounded-xl p-2 mb-6">
                <option value="">{{ __('messages.select_company') }}</option>
                @foreach ($suppliers as $supplier)
                    @if ($supplier->business)
                        <option value="{{ $supplier->id }}"
                            {{ $document->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->business->company_name }}
                        </option>
                    @endif
                @endforeach
            </select>

            <!-- نوع الوثيقة ورقمها -->
            <div class="mb-6">
                <label class="block mb-2 font-semibold text-[#212121]">
                    {{ __('messages.document_type') }}
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- نوع الوثيقة -->
                    <select id="document_name" name="document_name"
                        class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring focus:border-blue-400">
                        <option value="">{{ __('messages.select_document') }}</option>
                        <option value="National ID" {{ $document->document_name == 'National ID' ? 'selected' : '' }}>{{ __('messages.national_id') }}</option>
                        <option value="Tax Certificate" {{ $document->document_name == 'Tax Certificate' ? 'selected' : '' }}>{{ __('messages.tax_certificate') }}</option>
                        <option value="IBAN" {{ $document->document_name == 'IBAN' ? 'selected' : '' }}>{{ __('messages.iban') }}</option>
                        <option value="National Address" {{ $document->document_name == 'National Address' ? 'selected' : '' }}>{{ __('messages.national_address') }}</option>
                        <option value="Commercial Registration" {{ $document->document_name == 'Commercial Registration' ? 'selected' : '' }}>{{ __('messages.commercial_registration') }}</option>
                    </select>

                    <!-- قيمة الوثيقة -->
                    <input type="text" id="document_value_input" name="document_value"
                        value="{{ old('document_value', $document->document_value) }}"
                        placeholder="{{ __('messages.enter_document_number') }}"
                        class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring focus:border-blue-400">
                </div>

                <!-- أخطاء التحقق -->
                <div class="mt-2">
                    @error('document_name') 
                        <span class="text-red-500 text-sm block">{{ $message }}</span>
                    @enderror

                    @error('document_value') 
                        <span class="text-red-500 text-sm block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- رفع الملف -->
            <div class="mb-6">
                <label class="block mb-2 font-semibold text-[#212121]">
                    {{ __('messages.file') }}
                </label>

                <div class="relative flex items-center gap-2" id="current_file_wrapper">
                    <input type="text"
                           value="{{ $document->file_path ? basename($document->file_path) : __('messages.no_file_uploaded') }}"
                           readonly
                           class="flex-1 border border-gray-300 rounded-xl p-3 bg-gray-100 focus:outline-none">

                    @if ($document->file_path)
                        <a href="{{ asset('storage/' . $document->file_path) }}"
                           target="_blank"
                           class="ml-2 inline-block px-3 py-2 text-sm bg-[#185D31] text-white rounded hover:bg-[#157328]">
                            {{ __('messages.view_file') }}
                        </a>
                        <button type="button" id="remove_file_btn"
                                class="ml-2 text-[#212121] text-xl font-bold hover:text-red-800">
                            ×
                        </button>
                    @endif
                </div>

                <input type="file" name="file_path"
                       id="new_file_input"
                       class="w-full border border-gray-300 rounded-xl p-3 focus:outline-none focus:ring focus:border-blue-400 mt-2"
                       style="display: {{ $document->file_path ? 'none' : 'block' }};">

                <input type="hidden" name="remove_file" id="remove_file" value="0">

                @error('file_path')
                    <span class="text-red-500 text-sm block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- الحالة -->
            <div x-data="{ open: false }" class="mb-4 relative">
                <label class="block mb-2 font-bold">{{ __('messages.status') }}</label>

                <!-- الزر -->
                <button type="button"
                        @click="open = !open"
                        class="w-full border rounded-xl p-3 text-left flex justify-between items-center">
                    <span>
                        @php
                            $statuses = [
                                'expired' => __('messages.expired'),
                                'rejected' => __('messages.rejected'),
                                'pending' => __('messages.pending'),
                                'verified' => __('messages.verified'),
                            ];
                            $current = $statuses[$document->status] ?? __('messages.select_status');
                        @endphp
                        {{ $current }} 
                    </span>
                    <svg class="w-4 h-4 transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <!-- القائمة -->
                <div x-show="open" @click.away="open = false"
                     class="absolute z-10 mt-1 w-full bg-white border rounded-xl shadow p-2">
                    @foreach ($statuses as $key => $label)
                        <label class="flex items-center px-2 py-1 hover:bg-gray-100 rounded cursor-pointer">
                            <input type="radio"
                                   name="status"
                                   value="{{ $key }}"
                                   {{ $document->status == $key ? 'checked' : '' }}
                                   class="shrink-0 ml-3 w-5 h-5 border-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span>{{ $label }} </span>
                        </label>
                    @endforeach
                </div>

                @error('status')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- الملاحظات -->
            <div class="mb-4">
                <label class="block mb-2 font-bold">{{ __('messages.notes') }}</label>
                <textarea name="notes" placeholder="{{ __('messages.enter_notes') }}"
                 class="w-full border rounded-xl p-2">{{ old('notes', $document->notes) }}</textarea>
                @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- الأزرار -->
            <div class="flex gap-4">
                <button type="submit" class="bg-[#185D31] text-white px-4 py-2 rounded-xl">{{ __('messages.save') }}</button>
                <a href="{{ route('admin.documents.index') }}" class="bg-gray-300 text-black px-4 py-2 rounded-xl">{{ __('messages.cancel') }}</a>
            </div>

        </form>
    </div>
</main>
@endsection
