@extends('layouts.admin')

@section('page_title', __('messages.edit_supplier_account'))

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
    <div 
        x-data="editSupplierForm()" 
        class="bg-white rounded-b-xl shadow p-6 mx-auto"
    >
        <h2 class="text-[24px] font-bold text-[#212121] mb-6">{{ __('messages.edit_supplier_account') }}</h2>

        <div 
            x-show="success" 
            x-transition 
            class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-4"
            x-text="success"
        ></div>

        <template x-if="Object.keys(errors).length">
            <ul class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4 list-disc list-inside">
                <template x-for="[key, messages] of Object.entries(errors)" :key="key">
                    <li x-text="messages[0]"></li>
                </template>
            </ul>
        </template>

        <form @submit.prevent="submitForm" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.full_name') }}</label>
                    <input type="text" x-model="form.full_name"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.company_name') }}</label>
                    <input type="text" x-model="form.company_name"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.email') }}</label>
                    <input type="email" x-model="form.email"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>

                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.phone') }}</label>
                    <input type="text" x-model="form.phone_number"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.address') }}</label>
                    <input type="text" x-model="form.address"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                </div>

                {{-- Status Dropdown --}}
                <div x-data="{ 
        open: false, 
        selected: '{{ old('status', $supplier->status) }}' 
    }" 
    x-init="$watch('selected', value => form.status = value)" 
    class="relative w-full">
    
    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.account_status') }}</label>
    <button type="button"
        @click="open = !open"
        class="w-full flex justify-between items-center border border-gray-300 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-[#185D31] focus:border-[#185D31]">
        <span x-text="selected === 'active' ? '{{ __('messages.active') }}' : selected === 'inactive' ? '{{ __('messages.inactive') }}' : '{{ __('messages.banned') }}'"></span>
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" @click.away="open = false"
        class="absolute z-10 mt-2 w-full bg-white border border-gray-300 rounded-xl shadow-lg py-2">
        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="radio" value="active" x-model="selected"
                class="shrink-0 appearance-none rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="ml-2">{{ __('messages.active') }}</span>
        </label>
        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="radio" value="inactive" x-model="selected"
                class="shrink-0 appearance-none rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="ml-2">{{ __('messages.inactive') }}</span>
        </label>
        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="radio" value="banned" x-model="selected"
                class="shrink-0 appearance-none rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]">
            <span class="ml-2">{{ __('messages.banned') }}</span>
        </label>
    </div>

    <input type="hidden" x-model="form.status">
</div>
            </div>

          {{-- supplier_confirmed Checkbox --}}
            <div>
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input 
                        type="checkbox" 
                        x-model="form.supplier_confirmed" 
                        class="form-checkbox ml-4 h-5 w-5 rounded 
                               text-green-600 checked:bg-green-600 checked:border-green-600 focus:ring-green-600
                               accent-green-600">
                    <span class="text-[20px] font-bold text-[#212121]">{{ __('messages.supplier_confirmed') }}</span>
                </label>
            </div>

            <div class="flex space-x-4 mt-6">
                <button type="submit"
                    class="px-4 py-2 border ml-2 border-transparent rounded-[16px] shadow-sm text-sm font-medium text-white bg-[#185D31] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    {{ __('messages.update_account') }}
                </button>

                <button type="button" onclick="window.history.back()"
                    class="px-4 py-2 border border-gray-300 rounded-[16px] shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    {{ __('messages.cancel') }}
                </button>
            </div>
        </form>
    </div>
</main>


<script>
    function editSupplierForm() {
        return {
            form: {
                full_name: @json($supplier->full_name),
                company_name: @json(optional($supplier->business)->company_name),
                email: @json($supplier->email),
                phone_number: @json($supplier->phone_number),
                address: @json($supplier->address),
                status: @json($supplier->status),
                supplier_confirmed: @json(optional($supplier->business)->supplier_confirmed ?? false),

            },
            errors: {},
            success: '',
            async submitForm() {
                this.errors = {};
                this.success = '';
                try {
                    let response = await fetch("{{ route('admin.suppliers.update', $supplier) }}", {
                        method: 'PUT',
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
                        this.success = data.success || 'تم التحديث بنجاح';
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
