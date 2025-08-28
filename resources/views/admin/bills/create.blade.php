@extends('layouts.admin')

@section('page_title', __('messages.add_invoice'))

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
    <div class="bg-white rounded-xl shadow p-6 mx-auto" x-data="invoiceForm()">
        <h2 class="text-[24px] font-bold text-[#212121] mb-6">{{ __('messages.add_invoice') }}</h2>

        {{-- ✅ رسائل الأخطاء --}}
        <template x-if="errorMessages.length">
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    <template x-for="msg in errorMessages" :key="msg">
                        <li x-text="msg"></li>
                    </template>
                </ul>
            </div>
        </template>

        {{-- ✅ رسالة النجاح --}}
        <template x-if="successMessage">
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg" x-text="successMessage"></div>
        </template>

        <form @submit.prevent="submit" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_name" class="block font-bold text-[20px] text-[#212121]">
                        {{ __('messages.customer_name') }}
                    </label>
                    <input type="text" x-model="form.customer_name" name="customer_name" id="customer_name"
                        required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 rounded-xl">
                </div>

                <div>
                    <label for="order_number" class="block font-bold text-[20px] text-[#212121]">
                        {{ __('messages.order_number') }}
                    </label>
                    <input type="text" x-model="form.order_number" name="order_number" id="order_number"
                        required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 rounded-xl">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="total_price" class="block font-bold text-[20px] text-[#212121]">
                        {{ __('messages.total_price') }}
                    </label>
                    <input type="number" x-model="form.total_price" name="total_price" id="total_price"
                        required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 rounded-xl">
                </div>

                <div>
                    <label for="payment_way" class="block font-bold text-[20px] text-[#212121]">
                        {{ __('messages.payment_method') }}
                    </label>
                    <select x-model="form.payment_way" name="payment_way" id="payment_way"
                        required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 rounded-xl">
                        <option value="">{{ __('messages.select') }}</option>
                        <option value="cash">{{ __('messages.cash') }}</option>
                        <option value="bank_transfer">{{ __('messages.bank_transfer') }}</option>
                        <option value="credit_card">{{ __('messages.credit_card') }}</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="status" class="block font-bold text-[20px] text-[#212121]">
                    {{ __('messages.invoice_status') }}
                </label>
                <select x-model="form.status" name="status" id="status"
                    required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 rounded-xl">
                    <option value="payment">{{ __('messages.paid') }}</option>
                    <option value="not payment">{{ __('messages.unpaid') }}</option>
                    <option value="pending">{{ __('messages.pending') }}</option>
                </select>
            </div>

            <button type="submit"
                class="px-4 py-2 border ml-2 border-transparent rounded-[16px] shadow-sm text-sm font-medium text-white bg-[#185D31]">
                {{ __('messages.add_invoice') }}
            </button>
        </form>
    </div>
</main>

<script>
    function invoiceForm() {
        return {
            form: {
                customer_name: '',
                order_number: '',
                payment_way: '',
                total_price: '',
                status: 'payment',
            },
            errorMessages: [],
            successMessage: '',

            async submit() {
                this.errorMessages = [];
                this.successMessage = '';

                const formData = new FormData();
                Object.entries(this.form).forEach(([key, value]) => {
                    formData.append(key, value);
                });

                const response = await fetch('{{ route('invoices.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    this.successMessage = data.message;
                    this.form = { customer_name: '', order_number: '', payment_way: '', total_price: '', status: 'payment' };
                } else if (response.status === 422) {
                    for (const [field, msgs] of Object.entries(data.errors)) {
                        this.errorMessages.push(...msgs);
                    }
                } else if (data.message) {
                    this.errorMessages.push(data.message);
                } else {
                    this.errorMessages.push('{{ __('messages.unexpected_error') }}');
                }
            }
        }
    }
</script>
@endsection
