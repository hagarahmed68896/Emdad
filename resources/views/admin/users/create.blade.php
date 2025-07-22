@extends('layouts.admin') {{-- This line tells Laravel to use layouts/admin.blade.php as the base template --}}

@section('page_title', 'إضافة حساب جديد') {{-- This sets the title for this specific page, which will appear in the browser tab and the admin header --}}

@section('content') {{-- This is where the main content for this page goes, filling the @yield('content') slot in layouts/admin.blade.php --}}
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
    {{-- The 'main' tag here is within the @section('content') and will be placed inside the layout's main content area. --}}
    <div class="bg-white rounded-xl shadow p-6  mx-auto">
        <h2 class="text-[24px] font-bold text-[#212121] mb-6">إضافة حساب للعميل</h2>

        {{-- The HTML Form for adding a new user --}}
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf 
         <input type="hidden" name="account_type" value="customer">
            {{-- First Row: Full Name & Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name Input Field -->
                <div>
                    <label for="full_name" class="block font-bold text-[20px] text-[#212121]">الأسم </label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                    @error('full_name') 
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Input Field -->
                <div>
                    <label for="email" class="block font-bold text-[20px] text-[#212121]">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                </div>

            {{-- Second Row: Phone Number & Password --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone Number Input Field (Optional) -->
                <div>
                    <label for="phone_number" class="block font-bold text-[20px] text-[#212121]">رقم الهاتف</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                    @error('phone_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Address Input Field (Optional) -->
                <div>
                    <label for="address" class="block font-bold text-[20px] text-[#212121]">العنوان</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-green-500 focus:border-green-500 rounded-xl">
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            {{-- Third Row: Account Type & Status --}}
         <div 
    x-data="{ open: false, selected: '{{ old('status', 'active') }}' }" 
    class="relative w-full">

    <!-- Trigger button (full width) -->
    <label class="block font-bold text-[20px] text-[#212121]">حالة الحساب</label>
    <button type="button"
        @click="open = !open"
        class="w-full flex justify-between items-center border border-gray-300 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-[#185D31] focus:border-[#185D31]">
        <span x-text="selected === 'active' ? 'نشط' : selected === 'inactive' ? 'غير نشط' : 'محظور'"></span>
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <!-- Dropdown content (full width) -->
    <div x-show="open" @click.away="open = false"
        class="absolute z-10 mt-2 w-full bg-white border border-gray-300 rounded-xl shadow-lg py-2">

        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="radio" name="status_temp" value="active" x-model="selected"
                class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
             >
            <span class="ml-2">نشط</span>
        </label>

        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="radio" name="status_temp" value="inactive" x-model="selected"
             class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
             >
            <span class="ml-2">غير نشط</span>
        </label>

        <label class="flex items-center px-4 py-2 hover:bg-gray-100 cursor-pointer">
            <input type="radio" name="status_temp" value="banned" x-model="selected" 
             class="shrink-0 rtl:ml-3 ltr:mr-3 w-5 h-5 border-[#185D31] focus:ring-[#185D31] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
            >
            <span class="ml-2">محظور</span>
        </label>
    </div>

    <!-- Hidden real input for the form submission -->
    <input type="hidden" name="status" :value="selected">

    @error('status')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>




            {{-- Form Submission Buttons --}}
            <div class="flex space-x-4 mt-6"> 
                <button type="submit"
                        class="px-4 py-2 border ml-2 border-transparent rounded-[16px] shadow-sm text-sm font-medium text-white bg-[#185D31] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out ">
                    إضافة حساب
                </button>

                <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 border border-gray-300 rounded-[16px] shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out ">
                    إلغاء
                </button>
           
            </div>
        </form>
    </div>
</main>
@endsection
