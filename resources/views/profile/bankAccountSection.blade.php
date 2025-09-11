<section id="bankAccountSection" 
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ $section === 'bankAccountSection' ? '' : 'hidden' }}">

    <div id="bankMessage"></div>

    <form id="bankAccountForm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label font-bold">{{ __('messages.bank_name') }}</label>
            <input type="text" name="bank_name" 
                   value="{{ old('bank_name', $businessData->bank_name ?? '') }}"
                   class="form-control"
                   placeholder="{{ __('messages.bank_name') }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label font-bold">{{ __('messages.account_name') }}</label>
            <input type="text" name="account_name" 
                   value="{{ old('account_name', $businessData->account_name ?? '') }}"
                   class="form-control"
                   placeholder="{{ __('messages.account_name') }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label font-bold">{{ __('messages.bank_address') }}</label>
            <input type="text" name="bank_address" 
                   value="{{ old('bank_address', $businessData->bank_address ?? '') }}"
                   class="form-control"
                   placeholder="{{ __('messages.bank_address') }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label font-bold">{{ __('messages.swift_code') }}</label>
            <input type="text" name="swift_code" 
                   value="{{ old('swift_code', $businessData->swift_code ?? '') }}"
                   class="form-control"
                   placeholder="{{ __('messages.swift_code') }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="mb-3">
            <label class="form-label font-bold">{{ __('messages.iban') }}</label>
            <input type="text" name="iban" 
                   value="{{ old('iban', $businessData->iban ?? '') }}"
                   class="form-control"
                   placeholder="{{ __('messages.iban') }}">
            <div class="invalid-feedback"></div>
        </div>

        <div class="d-flex justify-content-start gap-2 mt-4">
            <button type="submit" class="btn btn-success px-4">{{ __('messages.save') }}</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</section>

<script>
document.getElementById('bankAccountForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);
    let messageBox = document.getElementById('bankMessage');

    // reset messages & field errors
    messageBox.innerHTML = '';
    form.querySelectorAll('.form-control').forEach(input => {
        input.classList.remove('is-invalid');
        input.nextElementSibling.innerHTML = '';
    });

    try {
        let response = await fetch("{{ route('business.bank.update') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData
        });

        let result = await response.json();

        if (response.ok && result.success) {
            messageBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        } else if (response.status === 422 && result.errors) {
            Object.keys(result.errors).forEach(field => {
                let input = form.querySelector(`[name="${field}"]`);
                if (input) {
                    input.classList.add('is-invalid');
                    input.nextElementSibling.innerHTML = result.errors[field][0];
                }
            });
        } else {
            messageBox.innerHTML = `<div class="alert alert-danger">${result.message ?? 'خطأ غير متوقع'}</div>`;
        }

    } catch (error) {
        messageBox.innerHTML = `<div class="alert alert-danger">حدث خطأ أثناء الاتصال بالخادم.</div>`;
    }
});
</script>
