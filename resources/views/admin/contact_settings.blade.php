@extends('layouts.admin')
@section('page_title', 'إعدادات التواصل')

@section('content')
<div class="p-6 bg-gray-50 h-screen overflow-y-auto"> 
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">إعدادات التواصل</h2>

  {{-- رسالة نجاح/فشل --}}
@if(request('message'))
    <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg shadow">
        {{ request('message') }}
    </div>
@endif


    <form action="{{ route('admin.contact.settings.store') }}" method="POST" class="space-y-4">
         {{-- @csrf --}}
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-2">العنوان</label>
                <input type="text" name="address" 
                       value="{{ old('address', $setting->address ?? '') }}" 
                       class="w-full rounded-lg border p-2">
            </div>

            <div>
                <label class="block mb-2">رقم الهاتف</label>
                <input type="text" name="phone" 
                       value="{{ old('phone', $setting->phone ?? '') }}" 
                       class="w-full rounded-lg border p-2">
            </div>
        </div>

        <div>
            <label class="block mb-2">البريد الإلكتروني</label>
            <input type="email" name="email" 
                   value="{{ old('email', $setting->email ?? '') }}" 
                   class="w-full rounded-lg border p-2">
        </div>

<div 
    x-data='{
        newPlatform: "",
        newUrl: "",
        socials: @json($setting->social_links ?? []),
        addSocial() {
            if (!this.newPlatform || !this.newUrl) return;

            // ✅ Force reactivity by creating a new object
            this.socials = { ...this.socials, [this.newPlatform]: this.newUrl };

            this.resetInputs();
        },
        resetInputs() {
            this.newPlatform = "";
            this.newUrl = "";
        },
        removeSocial(platform) {
            // ✅ Re-assign object after deleting
            const { [platform]: _, ...rest } = this.socials;
            this.socials = rest;
        }
    }'
>
    {{-- ✅ Label --}}
    <label class="block mb-1 font-bold">روابط منصات التواصل الاجتماعي</label>

    {{-- Input group --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
        {{-- Dropdown --}}
        <select x-model="newPlatform"
                class="border p-2 rounded-xl w-full sm:w-auto">
            <option value="">{{ __('اختر منصة') }}</option>
            <option value="facebook">Facebook</option>
            <option value="twitter">Twitter</option>
            <option value="linkedin">LinkedIn</option>
            <option value="youtube">Youtube</option>
        </select>

        {{-- Input --}}
        <input type="url"
               x-model="newUrl"
               placeholder="ضع الرابط هنا"
               class="border p-2 rounded-xl flex-1">

        {{-- Add Button --}}
        <button type="button"
                @click="addSocial"
                class="bg-[#185D31] text-white px-4 py-2 rounded-xl w-full sm:w-auto">
             إضافة
        </button>
    </div>

    {{-- ✅ Show socials --}}
    <div class="flex flex-col gap-2" x-show="Object.keys(socials).length > 0">
        <template x-for="(url, platform) in socials" :key="platform">
            <div class="bg-gray-100 rounded-xl px-4 py-2 flex items-center justify-between gap-3">
                {{-- Hidden input (to send to backend) --}}
                <input type="hidden" :name="`social_links[${platform}]`" :value="url">

                {{-- Platform name + link --}}
                <div class="flex items-center gap-2">
                    <span class="font-bold capitalize" x-text="platform"></span>
                    <a :href="url" target="_blank" class="text-blue-600 underline text-sm" x-text="url"></a>
                </div>

                {{-- Remove button --}}
                <button type="button"
                        @click="removeSocial(platform)"
                        class="text-red-500 text-sm font-bold">
                    ✕
                </button>
            </div>
        </template>
    </div>
</div>




        <div>
            <label class="block mb-2">حقوق النشر</label>
            <input type="text" name="copyrights" 
                   value="{{ old('copyrights', $setting->copyrights ?? '') }}" 
                   class="w-full border rounded-lg p-2">
        </div>

        <div class="flex gap-4 mt-6">
            <button type="submit" class="bg-[#185D31] text-white rounded-lg px-4 py-2">حفظ</button>
            <a href="{{ url()->previous() }}" class="bg-gray-300 rounded-lg px-4 py-2">تجاهل</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


@endsection
