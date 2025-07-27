@extends('layouts.admin')

@section('page_title', 'إضافة حساب جديد')

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
    <div 
        x-data="supplierForm()" x-cloak
        class="bg-white rounded-xl shadow p-6 mx-auto"
    >
        <h2 class="text-[24px] font-bold text-[#212121] mb-6">إضافة حساب للمورد</h2>

        {{-- ✅ عرض رسائل النجاح --}}
        <div 
            x-show="success" 
            x-transition 
            class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-4"
            x-text="success"
        ></div>

        {{-- ✅ عرض الأخطاء --}}
        <template x-if="Object.keys(errors).length">
            <ul class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4 list-disc list-inside">
                <template x-for="[key, messages] of Object.entries(errors)" :key="key">
                    <li x-text="messages[0]"></li>
                </template>
            </ul>
        </template>

        <form @submit.prevent="submitForm" class="space-y-6">
            @csrf

            <input type="hidden" name="account_type" value="supplier">

            {{-- First Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">الاسم</label>
                    <input type="text" x-model="form.full_name"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">اسم المورد</label>
                    <input type="text" x-model="form.company_name"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>
            </div>

            {{-- Second Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">البريد الإلكتروني</label>
                    <input type="email" x-model="form.email"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">رقم الهاتف</label>
                    <input type="text" x-model="form.phone_number"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>
            </div>

            {{-- Third Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">العنوان</label>
                    <input type="text" x-model="form.address"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>

                <div x-data="{ open: false }" class="relative w-full">
                    <label class="block font-bold text-[20px] text-[#212121]">حالة الحساب</label>
                    <button type="button"
                        @click="open = !open"
                        class="w-full flex justify-between items-center border border-gray-300 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-[#185D31] focus:border-[#185D31]">
                        <span x-text="form.status === 'active' ? 'نشط' : form.status === 'inactive' ? 'غير نشط' : 'محظور'"></span>
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        class="absolute z-10 mt-2 w-full bg-white border border-gray-300 rounded-xl shadow-lg py-2">
                        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            <input type="radio" value="active" x-model="form.status"
                                class="shrink-0 w-5 h-5 border-[#185D31] focus:ring-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="ml-2">نشط</span>
                        </label>

                        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            <input type="radio" value="inactive" x-model="form.status"
                                class="shrink-0 w-5 h-5 border-[#185D31] focus:ring-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="ml-2">غير نشط</span>
                        </label>

                        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
                            <input type="radio" value="banned" x-model="form.status"
                                class="shrink-0 w-5 h-5 border-[#185D31] focus:ring-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
                            <span class="ml-2">محظور</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex space-x-4 mt-6">
                <button type="submit"
                    class="px-4 py-2 border ml-2 border-transparent rounded-[16px] shadow-sm text-sm font-medium text-white bg-[#185D31] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    إضافة حساب
                </button>

                <button type="button" onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 rounded-[16px] shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    function supplierForm() {
        return {
            form: {
                full_name: '',
                company_name: '',
                email: '',
                phone_number: '',
                address: '',
                status: 'active',
                account_type: 'supplier',
            },
            errors: {},
            success: '',
            async submitForm() {
                this.errors = {};
                this.success = '';
                try {
                    let response = await fetch("{{ route('admin.suppliers.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form),
                    });

                    let data = await response.json();

                    if (!response.ok) {
                        this.errors = data.errors || {};
                    } else {
                        this.success = data.success || 'تم الحفظ بنجاح';
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.suppliers.index') }}";
                        }, 1500);
                    }
                } catch (error) {
                    console.error(error);
                }
            }
        };
    }
</script>
@endsection
