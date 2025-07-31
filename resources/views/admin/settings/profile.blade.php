@extends('layouts.admin')

@section('page_title', 'الملف الشخصي')

@section('content')

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 30px;
        border-radius: 1rem;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06);
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Style for validation errors specific to form fields */
    .input-error-message {
        color: #ef4444; /* red-500 */
        font-size: 0.875rem; /* text-sm */
        margin-top: 0.25rem; /* mt-1 */
    }
</style>

<div class="p-6 overflow-y-auto bg-white rounded-xl shadow mx-2">
    <p class="text-[32px] font-bold mb-6">الملف الشخصي</p>

    <div id="generalMessage" class="mb-4 hidden px-4 py-3 rounded text-center text-sm"></div>

    <div class="flex flex-col md:flex-row gap-8">
        <div class="flex flex-col items-center pb-6 border-gray-200 mb-6">
            <div class="relative w-28 h-28 mb-4">
                <img id="profilePageImage"
                    src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                    class="w-full h-full rounded-full object-cover shadow-md border-2 border-gray-300">
                <button id="openProfileModalBtn"
                    class="absolute bottom-0 left-0 bg-[#185D31] rounded-full p-2 shadow-md hover:bg-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M13.586 3.586a2 2 0 112.828 2.828l-7.65 7.65a2 2 0 01-1.287.587L5.5 15.5l.004-.004a2 2 0 01.587-1.287l7.65-7.65zM10.742 7.003L14 3.745l2.255 2.255-3.258 3.258a1 1 0 01-.482.261L10 9l-.004-.004a1 1 0 01-.261-.482l-3.258-3.258z" />
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <input type="file" id="profilePictureInput" accept="image/*" class="hidden">
            </div>
            <button id="saveProfilePhotoBtn"
                class="mt-2 px-4 py-2 bg-[#185D31] text-white rounded-lg shadow-md hidden">
                حفظ الصورة
            </button>
            <div id="uploadLoading" class="hidden mt-2 text-sm text-gray-600">جاري التحميل...</div>
            <div id="uploadMessage" class="mt-2 text-sm text-center"></div>

        </div>

        <div class="w-full md:w-3/4">
           <form id="profileForm" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="full_name" class="block mb-1 text-sm font-medium text-gray-700">الاسم</label>
                        <input type="text" name="full_name" id="full_name"
                            value="{{ old('full_name', $user->full_name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <div id="full_name_error" class="input-error-message"></div>
                    </div>
                    <div>
                        <label for="phone_number" class="block mb-1 text-sm font-medium text-gray-700">رقم الهاتف</label>
                        <input type="text" name="phone_number" id="phone_number"
                            value="{{ old('phone_number', $user->phone_number) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <div id="phone_number_error" class="input-error-message"></div>
                    </div>
                </div>
                <div>
                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email"
                        value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <div id="email_error" class="input-error-message"></div>
                </div>

                <div>
                    <label for="current_password" class="block mb-1 text-sm font-medium text-gray-700">كلمة المرور الحالية</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                       <!-- الزر الجديد مع الأيقونات المفتوحة والمغلقة -->
<span onclick="togglePassword('current_password', this)"
      class="absolute inset-y-0 left-0 flex items-center px-3 cursor-pointer">
    <!-- أيقونة إخفاء -->
    <svg class="eye-closed size-6 " xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.98 8.223A10.477 10.477 0 0 0
              1.934 12C3.226 16.338 7.244 19.5 12
              19.5c.993 0 1.953-.138
              2.863-.395M6.228 6.228A10.451 10.451 0 0 1
              12 4.5c4.756 0 8.773 3.162 10.065
              7.498a10.522 10.522 0 0 1-4.293
              5.774M6.228 6.228 3 3m3.228
              3.228 3.65 3.65m7.894 7.894L21
              21m-3.228-3.228-3.65-3.65m0 0a3 3 0
              1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
    </svg>
    <!-- أيقونة إظهار -->
    <svg class="eye-open size-6 hidden" xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5
              12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0
              .639C20.577 16.49 16.64 19.5 12 19.5c-4.638
              0-8.573-3.007-9.963-7.178Z" />
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>


</span>

                    </div>
                    <div id="current_password_error" class="input-error-message"></div>
                </div>

                <div>
                    <label for="password" class="block mb-1 text-sm font-medium text-gray-700">كلمة المرور الجديدة</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                  <!-- الزر الجديد مع الأيقونات المفتوحة والمغلقة -->
<span onclick="togglePassword('password', this)"
      class="absolute inset-y-0 left-0 flex items-center px-3 cursor-pointer">
    <!-- أيقونة إخفاء -->
    <svg class="eye-closed size-6 " xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.98 8.223A10.477 10.477 0 0 0
              1.934 12C3.226 16.338 7.244 19.5 12
              19.5c.993 0 1.953-.138
              2.863-.395M6.228 6.228A10.451 10.451 0 0 1
              12 4.5c4.756 0 8.773 3.162 10.065
              7.498a10.522 10.522 0 0 1-4.293
              5.774M6.228 6.228 3 3m3.228
              3.228 3.65 3.65m7.894 7.894L21
              21m-3.228-3.228-3.65-3.65m0 0a3 3 0
              1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
    </svg>
    <!-- أيقونة إظهار -->
    <svg class="eye-open size-6 hidden" xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5
              12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0
              .639C20.577 16.49 16.64 19.5 12 19.5c-4.638
              0-8.573-3.007-9.963-7.178Z" />
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>


</span>

                    </div>
                    <div id="password_error" class="input-error-message"></div>
                </div>

                <div>
                    <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                     <!-- الزر الجديد مع الأيقونات المفتوحة والمغلقة -->
<span onclick="togglePassword('password_confirmation', this)"
      class="absolute inset-y-0 left-0 flex items-center px-3 cursor-pointer">
    <svg class="eye-closed size-6 " xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3.98 8.223A10.477 10.477 0 0 0
              1.934 12C3.226 16.338 7.244 19.5 12
              19.5c.993 0 1.953-.138
              2.863-.395M6.228 6.228A10.451 10.451 0 0 1
              12 4.5c4.756 0 8.773 3.162 10.065
              7.498a10.522 10.522 0 0 1-4.293
              5.774M6.228 6.228 3 3m3.228
              3.228 3.65 3.65m7.894 7.894L21
              21m-3.228-3.228-3.65-3.65m0 0a3 3 0
              1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
    </svg>
    <!-- أيقونة إظهار -->
    <svg class="eye-open size-6 hidden" xmlns="http://www.w3.org/2000/svg"
         fill="none" viewBox="0 0 24 24"
         stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5
              12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0
              .639C20.577 16.49 16.64 19.5 12 19.5c-4.638
              0-8.573-3.007-9.963-7.178Z" />
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>


</span>

                    </div>
                    <div id="password_confirmation_error" class="input-error-message"></div>
                </div>

                <button type="submit"
                    class="px-6 py-3 bg-[#185D31] text-white rounded-lg shadow-md hover:bg-green-600">
                    حفظ التغييرات
                </button>
            </form>

        </div>
    </div>
</div>

<div id="profileModal" class="modal">
    <div class="modal-content text-center">
        <h3 class="text-2xl font-bold mb-6 text-gray-800">تغيير صورة الملف الشخصي</h3>

        <img id="modalProfileImage"
            src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/Unknown_person.jpg') }}"
            class="w-48 h-48 rounded-full object-cover mx-auto mb-6 border-4 border-gray-200 shadow-lg">

        <div class="flex flex-col space-y-4">
            <button id="changePhotoBtn"
                class="px-6 py-3 bg-[#185D31] text-white rounded-lg hover:bg-green-600">
                اختر صورة جديدة
            </button>

            @if ($user->profile_picture)
            <button id="removePhotoBtn"
                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700">
                إزالة الصورة الحالية
            </button>
            @endif

            <button id="closeModalBtn"
                class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                إغلاق
            </button>
        </div>

        <div id="modalMessage" class="mt-4 text-sm text-center"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileImage = document.getElementById('profilePageImage');
        const openProfileModalBtn = document.getElementById('openProfileModalBtn');
        const profilePictureInput = document.getElementById('profilePictureInput');
        const saveProfilePhotoBtn = document.getElementById('saveProfilePhotoBtn');
        const uploadLoading = document.getElementById('uploadLoading');
        const uploadMessage = document.getElementById('uploadMessage');
        const profileModal = document.getElementById('profileModal');
        const modalProfileImage = document.getElementById('modalProfileImage');
        const changePhotoBtn = document.getElementById('changePhotoBtn');
        const removePhotoBtn = document.getElementById('removePhotoBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalMessage = document.getElementById('modalMessage');
        const generalMessage = document.getElementById('generalMessage');
        const profileForm = document.getElementById('profileForm');

        // Function to clear all input-specific error messages
        function clearInputErrors() {
            document.querySelectorAll('.input-error-message').forEach(el => {
                el.textContent = '';
            });
            document.querySelectorAll('input').forEach(input => {
                input.classList.remove('border-red-500'); // Remove red border
            });
        }

        openProfileModalBtn.addEventListener('click', () => {
            profileModal.classList.add('show');
        });

        closeModalBtn.addEventListener('click', () => {
            profileModal.classList.remove('show');
        });

        changePhotoBtn.addEventListener('click', () => {
            profilePictureInput.click();
        });

        profilePictureInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                saveProfilePhotoBtn.classList.remove('hidden');
                uploadMessage.textContent = 'تم اختيار صورة جديدة.';
            }
        });

        saveProfilePhotoBtn.addEventListener('click', () => {
            const file = profilePictureInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_picture', file);
            formData.append('_token', '{{ csrf_token() }}');

            uploadLoading.classList.remove('hidden');
            uploadMessage.textContent = ''; // Clear previous messages
            modalMessage.textContent = ''; // Clear modal messages

            fetch('{{ route('profile.photo.upload') }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                uploadLoading.classList.add('hidden');
                if (data.profile_picture_url) {
                    const newUrl = data.profile_picture_url + '?v=' + new Date().getTime();
                    profileImage.src = newUrl;
                    modalProfileImage.src = newUrl;

                    uploadMessage.textContent = '✅ تم رفع الصورة بنجاح.';
                    profileModal.classList.remove('show');
                    saveProfilePhotoBtn.classList.add('hidden');
                    // Show a general success message on the page
                    generalMessage.textContent = '✅ تم تحديث صورة الملف الشخصي بنجاح.';
                    generalMessage.className = 'mb-4 px-4 py-3 rounded text-center text-sm bg-green-100 text-green-800 border border-green-400';
                    setTimeout(() => { generalMessage.classList.add('hidden'); }, 5000); // Hide after 5 seconds
                } else {
                    modalMessage.textContent = '⚠️ حدث خطأ ما أثناء رفع الصورة.';
                    modalMessage.className = 'mt-4 text-sm text-center text-red-600';
                }
            })
            .catch(() => {
                uploadLoading.classList.add('hidden');
                modalMessage.textContent = '⚠️ فشل الرفع. يرجى المحاولة مرة أخرى.';
                modalMessage.className = 'mt-4 text-sm text-center text-red-600';
            });
        });

        // Event listener for removing profile picture
        @if ($user->profile_picture)
        removePhotoBtn.addEventListener('click', () => {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE'); // Use DELETE method for removal

            uploadLoading.classList.remove('hidden');
            modalMessage.textContent = ''; // Clear previous messages

            fetch('{{ route('profile.photo.delete') }}', {
                method: 'POST', // Fetch sends DELETE as POST with _method
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                uploadLoading.classList.add('hidden');
                if (data.success) {
                    profileImage.src = '{{ asset('images/Unknown_person.jpg') }}';
                    modalProfileImage.src = '{{ asset('images/Unknown_person.jpg') }}';
                    removePhotoBtn.remove(); // Remove the button itself
                    profileModal.classList.remove('show');
                    uploadMessage.textContent = 'تمت إزالة الصورة بنجاح.';
                    saveProfilePhotoBtn.classList.add('hidden'); // Hide save button
                     generalMessage.textContent = '✅ تم إزالة صورة الملف الشخصي بنجاح.';
                    generalMessage.className = 'mb-4 px-4 py-3 rounded text-center text-sm bg-green-100 text-green-800 border border-green-400';
                    setTimeout(() => { generalMessage.classList.add('hidden'); }, 5000); // Hide after 5 seconds

                } else {
                    modalMessage.textContent = data.message || '⚠️ حدث خطأ أثناء إزالة الصورة.';
                    modalMessage.className = 'mt-4 text-sm text-center text-red-600';
                }
            })
            .catch(() => {
                uploadLoading.classList.add('hidden');
                modalMessage.textContent = '⚠️ فشل الاتصال بالخادم لإزالة الصورة.';
                modalMessage.className = 'mt-4 text-sm text-center text-red-600';
            });
        });
        @endif

        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            clearInputErrors(); // Clear existing errors on new submission
            generalMessage.classList.add('hidden'); // Hide general message initially
            generalMessage.textContent = ''; // Clear general message content

            const formData = new FormData(this);

            fetch('{{ route('profile.update') }}', {
                method: 'POST', // Use POST for PUT requests with _method field
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json' // Crucial to tell Laravel we expect JSON response
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json(); // Always parse JSON
                if (res.ok) {
                    generalMessage.textContent = data.message || '✅ تم حفظ التغييرات بنجاح.';
                    generalMessage.className = 'mb-4 px-4 py-3 rounded text-center text-sm bg-green-100 text-green-800 border border-green-400';
                    // Clear password fields on successful update if they were submitted
                    document.getElementById('current_password').value = '';
                    document.getElementById('password').value = '';
                    document.getElementById('password_confirmation').value = '';
                } else if (res.status === 422 && data.errors) {
                    // Validation errors
                    // generalMessage.textContent = '⚠️ يرجى تصحيح الأخطاء التالية:';
                    generalMessage.className = 'mb-4 px-4 py-3 rounded text-center text-sm bg-red-100 text-red-800 border border-red-400';

                    for (const field in data.errors) {
                        const errorDiv = document.getElementById(`${field}_error`);
                        const inputField = document.getElementById(field);
                        if (errorDiv) {
                            errorDiv.textContent = data.errors[field][0]; // Display the first error message
                            if (inputField) {
                                inputField.classList.add('border-red-500'); // Add red border to problematic input
                            }
                        }
                    }
                } else {
                    // Other errors (e.g., 500 internal server error)
                    generalMessage.textContent = data.message || '⚠️ حدث خطأ غير متوقع. يرجى المحاولة لاحقًا.';
                    generalMessage.className = 'mb-4 px-4 py-3 rounded text-center text-sm bg-red-100 text-red-800 border border-red-400';
                }
                generalMessage.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Fetch error:', error);
                generalMessage.textContent = '⚠️ فشل الاتصال بالخادم. يرجى التحقق من اتصالك بالإنترنت.';
                generalMessage.className = 'mb-4 px-4 py-3 rounded text-center text-sm bg-red-100 text-red-800 border border-red-400';
                generalMessage.classList.remove('hidden');
            });
        });
    });

function togglePassword(fieldId, iconContainer) {
    const field = document.getElementById(fieldId);
    const eyeOpen = iconContainer.querySelector('.eye-open');
    const eyeClosed = iconContainer.querySelector('.eye-closed');

    if (field.type === 'password') {
        field.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        field.type = 'password';
        eyeClosed.classList.add('hidden');
        eyeOpen.classList.remove('hidden');
    }
}

</script>

@endsection