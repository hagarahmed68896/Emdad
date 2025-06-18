@extends('layouts.app')

@section('content')
    <style>
        /* Modal specific styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: 1rem; /* Tailwind's rounded-xl equivalent */
            width: 90%; /* Responsive width */
            max-width: 500px; /* Max width */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Tailwind's shadow-lg equivalent */
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="min-h-screen flex flex-col items-center py-8">
        <div class="w-full max-w-7xl bg-white rounded-xl shadow-lg flex flex-col md:flex-row overflow-hidden">
            <!-- Sidebar / Navigation -->
            <aside class="w-full md:w-1/4 bg-gray-50 p-6 border-b md:border-b-0 md:border-l border-gray-200">
                <div class="flex flex-col items-center pb-6 border-b border-gray-200 mb-6">
                    <!-- Profile Image and Upload Section -->
                    <div class="relative w-28 h-28 mb-4">
                        {{-- The profile image source. It checks if Auth::user()->profile_picture exists, otherwise uses your specified fallback. --}}
                        {{-- Changed id from profileImage to profilePageImage to avoid conflict with header --}}
                        <img id="profilePageImage" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                             alt="User Profile" class="w-full h-full rounded-full object-cover shadow-md border-2 border-gray-300">
                        <!-- Pen Icon Button to open the profile image management modal -->
                        <button id="openProfileModalBtn" class="absolute bottom-0 left-0 bg-[#185D31] rounded-full p-2 shadow-md hover:bg-green-600 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-400">
                            {{-- SVG Pen Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-7.65 7.65a2 2 0 01-1.287.587L5.5 15.5l.004-.004a2 2 0 01.587-1.287l7.65-7.65zM10.742 7.003L14 3.745l2.255 2.255-3.258 3.258a1 1 0 01-.482.261L10 9l-.004-.004a1 1 0 01-.261-.482l-3.258-3.258z" />
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <!-- Hidden File Input: This input is triggered by JavaScript from within the modal -->
                        <input type="file" id="profilePictureInput" accept="image/*" class="hidden">
                    </div>
                    <!-- Save Profile Photo Button: Hidden by default, appears after a new image is selected via the modal -->
                    <button id="saveProfilePhotoBtn" class="mt-2 px-4 py-2 bg-[#185D31] text-white rounded-lg shadow-md hover:bg-[#185D31] transition-colors duration-200 hidden">
                        حفظ الصورة
                    </button>
                    <!-- Loading Indicator for profile photo upload -->
                    <div id="uploadLoading" class="hidden mt-2 text-sm text-gray-600">جار التحميل...</div>
                    <!-- Message area for profile photo upload status (success/error) -->
                    <div id="uploadMessage" class="mt-2 text-sm text-center"></div>

                    {{-- User's full name displayed --}}
                    <h2 class="text-xl font-semibold text-gray-800">{{ Auth::user()->full_name }} </h2>
                </div>

                <nav>
                    <ul class="space-y-2">
                        <li>
                            <a href="#" class="flex items-center p-3 text-lg font-medium text-green-700 bg-green-100 rounded-lg hover:text-white hover:bg-[#185D31] transition-colors duration-200 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                حسابي
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:text-white hover:bg-[#185D31] transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                طلباتي
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-lg text-gray-700 rounded-lg  hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                الرسائل
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                المفضلة
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                إعدادات الإشعارات
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-3 text-lg text-gray-700 rounded-lg hover:bg-red-100 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                تسجيل الخروج
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="w-full md:w-3/4 p-6 bg-white">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">حسابي</h1>
                    <p class="text-sm text-gray-500">الرئيسية <span class="mx-1">&gt;</span> حسابي</p>
                </div>

                <!-- Account Details Section -->
                <section class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">تفاصيل الحساب</h2>
                    <form action="{{ route('profile.updateDetails') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="first_name" class="block text-gray-700 text-sm font-medium mb-2">الاسم
                                    الأول</label>
                                <input type="text" id="first_name" name="first_name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل اسمك الأول" value="{{ $user->first_name }}">
                                @error('first_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-gray-700 text-sm font-medium mb-2">الاسم
                                    الأخير</label>
                                <input type="text" id="last_name" name="last_name"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل اسم العائلة" value="{{ $user->last_name }}">
                                @error('last_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">البريد
                                    الإلكتروني</label>
                                <input type="email" id="email" name="email"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل بريدك الإلكتروني"
                                    value="{{ $user->email ?? 'ahmed.mohamed@example.com' }}">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone_number" class="block text-gray-700 text-sm font-medium mb-2">رقم
                                    الهاتف</label>
                                <input type="text" id="phone_number" name="phone_number"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل رقم هاتفك" value="{{ $user->phone_number ?? '+966501234567' }}">
                                @error('phone_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="address" class="block text-gray-700 text-sm font-medium mb-2">عنوان</label>
                                <input type="text" id="address" name="address"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل عنوانك"
                                    value="{{ $user->address ?? 'الرياض، المملكة العربية السعودية' }}">
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-start gap-4">
                            <button type="submit"
                                class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">حفظ</button>
                        </div>
                    </form>
                </section>

                <!-- Password Section -->
                <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">كلمة المرور</h2>
                    <form action="{{ route('profile.updatePassword') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="current_password" class="block text-gray-700 text-sm font-medium mb-2">كلمة
                                    المرور القديمة</label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل كلمة المرور القديمة">
                                @error('current_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">كلمة المرور
                                    الجديدة</label>
                                <input type="password" id="password" name="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أدخل كلمة المرور الجديدة">
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="password_confirmation"
                                    class="block text-gray-700 text-sm font-medium mb-2">تأكيد كلمة المرور</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition-all duration-200 outline-none"
                                    placeholder="أعد إدخال كلمة المرور">
                                @error('password_confirmation')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex justify-start gap-4">
                            <button type="submit"
                                class="px-6 py-2 text-white rounded-lg bg-[#185D31] transition-colors duration-200 shadow-md">حفظ</button>
                        </div>
                    </form>
                </section>

                <!-- Success/Error Messages (Optional, for Laravel Flash Messages) -->
                @if (session('success'))
                    <div class="mt-4 p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mt-4 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
            </main>
        </div>
    </div>

    <!-- Profile Picture Management Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content text-center">
            <h3 class="text-2xl font-bold mb-6 text-gray-800">إدارة صورة الملف الشخصي</h3>
            {{-- Image displayed within the modal, showing current profile picture or your specified placeholder --}}
            <img id="modalProfileImage" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/Unknown_person.jpg') }}"
                 alt="Current Profile" class="w-48 h-48 rounded-full object-cover mx-auto mb-6 border-4 border-gray-200 shadow-lg">

            <div class="flex flex-col space-y-4">
                {{-- Button to trigger the hidden file input for changing the photo --}}
                <button id="changePhotoBtn" class="flex items-center justify-center px-6 py-3 bg-[#185D31] text-white rounded-lg shadow-md hover:bg-[#185D31] transition-colors duration-200 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L20 16m-2-6a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                    تغيير الصورة
                </button>
                {{-- Button to remove the current profile picture. Only shown if a profile picture exists. --}}
                @if (Auth::user()->profile_picture)
                <button id="removePhotoBtn" class="flex items-center justify-center px-6 py-3 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition-colors duration-200 text-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    إزالة الصورة
                </button>
                @endif
                {{-- Button to close the modal --}}
                <button id="closeModalBtn" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors duration-200 shadow-sm text-lg">
                    إلغاء
                </button>
            </div>
            <!-- Message area for modal actions, e.g., success/error after remove action -->
            <div id="modalMessage" class="mt-4 text-sm text-center"></div>
        </div>
    </div>

    {{-- JavaScript for handling profile picture interactions and AJAX uploads --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get references to main page elements
            // IMPORTANT: Changed profileImage to profilePageImage to avoid ID conflict
            const profileImage = document.getElementById('profilePageImage'); // The main profile image displayed
            const openProfileModalBtn = document.getElementById('openProfileModalBtn'); // Pen icon button to open modal
            const profilePictureInput = document.getElementById('profilePictureInput'); // Hidden file input
            const saveProfilePhotoBtn = document.getElementById('saveProfilePhotoBtn'); // Button to save new photo
            const uploadLoading = document.getElementById('uploadLoading'); // Loading indicator
            const uploadMessage = document.getElementById('uploadMessage'); // Message area for upload status
            
            // Get references to modal elements
            const profileModal = document.getElementById('profileModal'); // The modal container
            const modalProfileImage = document.getElementById('modalProfileImage'); // Image displayed inside modal
            const changePhotoBtn = document.getElementById('changePhotoBtn'); // "Change Photo" button inside modal
            const removePhotoBtn = document.getElementById('removePhotoBtn'); // "Remove Photo" button inside modal (can be null if no picture)
            const closeModalBtn = document.getElementById('closeModalBtn'); // "Cancel" button inside modal
            const modalMessage = document.getElementById('modalMessage'); // Message area inside modal

            let selectedFile = null; // Variable to hold the file selected by the user before upload

            // --- Modal Open/Close Logic ---
            // When the pen icon button is clicked, open the modal
            openProfileModalBtn.addEventListener('click', function() {
                modalProfileImage.src = profileImage.src; // Ensure modal shows the current main profile image
                profileModal.style.display = 'flex'; // Display the modal using flexbox for centering
                modalMessage.textContent = ''; // Clear any previous messages shown in the modal
            });

            // When the "Cancel" button inside the modal is clicked, close the modal
            closeModalBtn.addEventListener('click', function() {
                profileModal.style.display = 'none';
            });

            // Close the modal if the user clicks anywhere outside of the modal content
            window.addEventListener('click', function(event) {
                if (event.target == profileModal) {
                    profileModal.style.display = 'none';
                }
            });

            // --- Change Photo (via hidden input triggered from modal) ---
            // When the "Change Photo" button inside the modal is clicked
            changePhotoBtn.addEventListener('click', function() {
                profilePictureInput.click(); // Programmatically click the hidden file input
                // IMPORTANT: The modal closes immediately here. The preview will happen on the main page.
                profileModal.style.display = 'none'; 
            });

            // --- Handle File Selection (from hidden input after modal closes) ---
            // When a file is selected using the hidden input
            profilePictureInput.addEventListener('change', function(event) {
                const file = event.target.files[0]; // Get the selected file
                if (file) {
                    selectedFile = file; // Store the file for later upload
                    const reader = new FileReader(); // Create a FileReader to read the file
                    reader.onload = function(e) {
                        console.log('FileReader loaded. Updating profilePageImage.src to:', e.target.result);
                        profileImage.src = e.target.result; // Display a real-time preview on the main page
                        saveProfilePhotoBtn.classList.remove('hidden'); // Show the "Save Photo" button
                        uploadMessage.textContent = ''; // Clear any previous messages in the main area
                    };
                    reader.readAsDataURL(file); // Read the file as a data URL (this triggers reader.onload asynchronously)
                } else {
                    console.log('No file selected in input. Clearing selected file and hiding save button.');
                    selectedFile = null; // No file selected, clear the stored file
                    saveProfilePhotoBtn.classList.add('hidden'); // Hide the "Save Photo" button
                    // IMPORTANT: Do NOT revert profileImage.src here. It should only revert if explicitly removed or on page reload if no new file is saved.
                }
                // Reset the input value so selecting the same file again triggers the 'change' event
                event.target.value = ''; 
            });

            // --- Handle Save Profile Photo Button Click (AJAX upload) ---
            // When the "Save Photo" button on the main page is clicked
            saveProfilePhotoBtn.addEventListener('click', async function() {
                if (!selectedFile) {
                    // If no file is selected but button is somehow clicked, show an error
                    uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                    uploadMessage.textContent = 'الرجاء تحديد صورة لتحميلها.'; // Please select an image to upload.
                    console.log('Save button clicked but no file selected.');
                    return;
                }

                uploadLoading.classList.remove('hidden'); // Show the loading indicator
                saveProfilePhotoBtn.classList.add('hidden'); // Hide the "Save Photo" button during upload
                uploadMessage.textContent = ''; // Clear previous messages in the main area
                console.log('Attempting to upload file:', selectedFile.name);

                const formData = new FormData(); // Create a new FormData object to send the file
                formData.append('profile_picture', selectedFile); // Append the selected file
                formData.append('_token', '{{ csrf_token() }}'); // Append the CSRF token for Laravel security

                try {
                    // Send an AJAX POST request to the updateProfilePicture route
                    const response = await fetch('{{ route('profile.updateProfilePicture') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json' // Tell the server we expect a JSON response
                        }
                    });

                    const data = await response.json(); // Parse the JSON response from the server
                    console.log('Upload response received:', data);

                    uploadLoading.classList.add('hidden'); // Hide the loading indicator

                    if (response.ok) { // If the HTTP status code is 2xx (success)
                        // Add timestamp to URL to bust browser cache
                        const newImageUrl = data.profile_picture_url + '?' + new Date().getTime();
                        profileImage.src = newImageUrl; // Update the main image with the new URL from the server
                        modalProfileImage.src = newImageUrl; // Update modal image source too
                        
                        uploadMessage.className = 'mt-2 text-sm text-green-500 text-center';
                        uploadMessage.textContent = data.message || 'تم تحديث صورة الملف الشخصي بنجاح!'; // Display success message
                        selectedFile = null; // Clear the selected file reference
                        profilePictureInput.value = ''; // Reset the hidden file input

                        // If the remove button exists and was hidden (e.g., initially no photo), make it visible again
                        if (removePhotoBtn) { 
                            removePhotoBtn.classList.remove('hidden');
                        }
                        console.log('Profile picture updated successfully. New URL:', newImageUrl);

                    } else { // If the HTTP status code is not 2xx (e.g., 4xx, 5xx)
                        let errorMessage = 'حدث خطأ أثناء تحميل الصورة.'; // Default error message
                        if (data.message) {
                            errorMessage = data.message; // Use server-provided message
                        } else if (data.errors && data.errors.profile_picture) {
                            errorMessage = data.errors.profile_picture[0]; // Display specific validation error
                        }
                        uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                        uploadMessage.textContent = errorMessage;
                        saveProfilePhotoBtn.classList.remove('hidden'); // Show the "Save Photo" button again for retry
                        console.error('Profile picture upload failed:', errorMessage);
                    }
                } catch (error) {
                    console.error('Error during profile picture upload fetch:', error);
                    uploadLoading.classList.add('hidden');
                    saveProfilePhotoBtn.classList.remove('hidden');
                    uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                    uploadMessage.textContent = 'حدث خطأ غير متوقع. حاول مرة أخرى.'; // Display unexpected error message
                }
            });

            // --- Handle Remove Photo Button Click ---
            // Ensure the removePhotoBtn exists before adding a listener (it's conditionally rendered)
            if (removePhotoBtn) {
                removePhotoBtn.addEventListener('click', async function() {
                    // No confirm() dialog as per your request
                    console.log('Attempting to remove profile picture.');

                    profileModal.style.display = 'none'; // Close the modal immediately
                    uploadLoading.classList.remove('hidden'); // Show loading indicator on the main page
                    uploadMessage.textContent = ''; // Clear any main page messages

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token

                    try {
                        // Send an AJAX POST request to the removeProfilePicture route
                        const response = await fetch('{{ route('profile.removeProfilePicture') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json(); // Parse JSON response
                        console.log('Remove response received:', data);

                        uploadLoading.classList.add('hidden'); // Hide loading indicator

                        if (response.ok) { // If successful
                            // Add timestamp to placeholder URL to bust browser cache
                            const placeholderUrlWithCacheBuster = '{{ asset('images/Unknown_person.jpg') }}' + '?' + new Date().getTime();
                            profileImage.src = placeholderUrlWithCacheBuster; // Set main image to your specified placeholder
                            modalProfileImage.src = placeholderUrlWithCacheBuster; // Set modal image to your specified placeholder
                            
                            uploadMessage.className = 'mt-2 text-sm text-green-500 text-center';
                            uploadMessage.textContent = data.message || 'تمت إزالة صورة الملف الشخصي بنجاح.'; // Display success message
                            saveProfilePhotoBtn.classList.add('hidden'); // Hide the "Save Photo" button (no new image selected)
                            if (removePhotoBtn) { // Hide remove button in modal if it was present
                                removePhotoBtn.classList.add('hidden');
                            }
                            console.log('Profile picture removed successfully. Displaying placeholder:', placeholderUrlWithCacheBuster);
                        } else { // If an error occurred during removal
                            let errorMessage = 'حدث خطأ أثناء إزالة الصورة.';
                            if (data.message) {
                                errorMessage = data.message;
                            }
                            uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                            uploadMessage.textContent = errorMessage;
                            console.error('Profile picture removal failed:', errorMessage);
                        }
                    } catch (error) {
                        console.error('Error during profile picture removal fetch:', error);
                        uploadLoading.classList.add('hidden');
                        uploadMessage.className = 'mt-2 text-sm text-red-500 text-center';
                        uploadMessage.textContent = 'حدث خطأ غير متوقع أثناء الإزالة. حاول مرة أخرى.';
                    }
                });
            }
        });
    </script>
@endsection
