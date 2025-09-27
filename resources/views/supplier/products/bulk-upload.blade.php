@extends('layouts.app')

@section('content')
<div class=" py-10 mx-[64px]">
    {{-- Page Title --}}
    <h2 class="text-3xl font-bold mb-6 text-gray-800">{{ __('messages.bulk_product_upload') }}</h2>

    {{-- Success / Error Message --}}
    <div id="uploadMessage" class="hidden mb-6 p-4 rounded text-white"></div>

    {{-- Template Download --}}
    <div class="mb-6">
        <a href="{{ url('/products/bulk-upload-template') }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                 stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      d="M4 16v4h16v-4M12 12v8m0-8l-4 4m4-4l4 4M4 4h16v8H4V4z"/>
            </svg>
            {{ __('messages.download_template') }}
        </a>
    </div>

    {{-- Upload Form --}}
    <form id="bulkUploadForm" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block mb-2 font-semibold text-gray-700">{{ __('messages.choose_file_label') }}</label>
            <input type="file" name="file" accept=".xlsx,.csv" required
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-green-500">
        </div>

        <button type="submit" 
                class=" flex justify-center items-center px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                 stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" 
                      d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('messages.upload_products_button') }}
        </button>
    </form>
</div>

{{-- AJAX for instant success/error display --}}
<script>
document.getElementById('bulkUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const messageDiv = document.getElementById('uploadMessage');

    fetch("{{ route('products.bulkUpload') }}", {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.classList.remove('hidden');
        if (data.success) {
            messageDiv.textContent = data.success;
            messageDiv.classList.remove('bg-red-500');
            messageDiv.classList.add('bg-green-600');
        } else if (data.error) {
            messageDiv.textContent = data.error;
            messageDiv.classList.remove('bg-green-600');
            messageDiv.classList.add('bg-red-500');
        }
    })
    .catch(error => {
        messageDiv.classList.remove('hidden', 'bg-green-600');
        messageDiv.classList.add('bg-red-500');
        messageDiv.textContent = 'حدث خطأ أثناء رفع المنتجات';
        console.error(error);
    });
});
</script>
@endsection
