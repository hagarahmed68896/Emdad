@extends('layouts.admin')

@section('page_title', __('messages.add_settlement'))

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">

    {{-- Header with title and breadcrumbs/links if needed --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">{{ __('messages.add_settlement') }}</h2>
    </div>

    {{-- Validation errors container --}}
    <div id="error-messages" class="hidden bg-red-100 border border-red-300 text-red-800 p-4 rounded-lg mb-6 shadow-sm">
        <ul id="error-list" class="list-disc pl-5 space-y-1 text-sm"></ul>
    </div>

    {{-- Main form card --}}
    <div class="bg-white rounded-xl p-8 shadow-lg max-h-[80vh] overflow-y-auto">
        <form id="settlementForm" action="{{ route('settlements.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- Form fields grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                {{-- Supplier --}}
                <div>
                    <label for="supplier_id" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.supplier') }}</label>
                    <select name="supplier_id" id="supplier_id"
                            class="form-input w-full border border-gray-300 rounded-lg py-2.5 px-3 shadow-sm focus:border-green-500 focus:ring-green-500 focus:ring-1 transition-all duration-200">
                        <option value="">{{ __('messages.choose_supplier') }}</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Request Number --}}
                <div>
                    <label for="request_number" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.request_number') }}</label>
                    <select name="request_number" id="request_number"
                            class="form-input w-full border border-gray-300 rounded-lg py-2.5 px-3 shadow-sm focus:border-green-500 focus:ring-green-500 focus:ring-1 transition-all duration-200">
                        <option value="">{{ __('messages.choose_order') }}</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}">#{{ $order->order_number }} - {{ $order->total_amount }} {{ __('messages.currency') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Amount & Status grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                {{-- Amount --}}
                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.amount') }}</label>
                    <input type="number" step="0.01" name="amount" id="amount"
                           class="form-input w-full border border-gray-300 rounded-lg px-4 py-2.5 shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all duration-200"
                           placeholder="0.00">
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.status') }}</label>
                    <select name="status" id="status"
                            class="form-input w-full border border-gray-300 rounded-lg py-2.5 px-3 shadow-sm focus:border-green-500 focus:ring-green-500 focus:ring-1 transition-all duration-200">
                        <option value="pending">{{ __('messages.pending') }}</option>
                        <option value="transferred">{{ __('messages.transferred') }}</option>
                    </select>
                </div>
            </div>

            {{-- Settlement Date --}}
            <div>
                <label for="settlement_date" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.settlement_date') }}</label>
                <input type="date" name="settlement_date" id="settlement_date"
                       class="form-input w-full border border-gray-300 rounded-lg px-4 py-2.5 shadow-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 transition-all duration-200">
            </div>

            {{-- Action buttons --}}
            <div class="flex items-center gap-4 pt-6">
                <a href="{{ route('settlements.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gray-100 text-gray-600 font-semibold hover:bg-gray-200 transition-colors duration-200">
                    {{ __('messages.back') }}
                </a>
                <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-green-600 text-white font-semibold shadow-md hover:bg-green-700 transition-colors duration-200">
                    {{ __('messages.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- AJAX Form Submission --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('settlementForm');
        const errorMessages = document.getElementById('error-messages');
        const errorList = document.getElementById('error-list');
        const saveButton = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            saveButton.disabled = true;
            saveButton.classList.add('opacity-50', 'cursor-not-allowed');

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = "{{ route('settlements.index') }}";
                } else {
                    // Display validation errors
                    errorList.innerHTML = "";
                    for (const field in data.errors) {
                        data.errors[field].forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                    }
                    errorMessages.classList.remove('hidden');
                }
            } catch (err) {
                alert("{{ __('messages.error_occurred') }}");
            } finally {
                saveButton.disabled = false;
                saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });
    });
</script>
@endsection