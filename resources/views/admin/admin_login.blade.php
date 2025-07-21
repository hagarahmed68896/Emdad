
    <!-- Tailwind CSS CDN (should ideally be in a layout file's head, but placed here as per request) -->
    <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

<div dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
     style="font-family: 'Cairo', sans-serif;"
     class="flex flex-col rtl md:flex-row w-full h-screen bg-white rounded-2xl overflow-hidden">
        <!-- Left Section: Login Form -->
      

        <!-- Right Section: Dashboard Preview (Green Background) -->
        <div class="w-full md:w-1/2 bg-[#185D31] p-8 md:p-12 flex flex-col justify-center items-center text-white overflow-auto">
               <!-- Placeholder for dashboard image -->
            <div class="w-full max-w-md bg-white rounded-xl p-4 shadow-lg mb-4">
                <img src="/images/cd11071e9281b541cdbadadda2e9d1fcb71ad2cb.png" alt="Dashboard Preview" class="w-full h-auto rounded-lg">
            </div>
            <h2 class="text-4xl font-bold text-center mb-6">مرحباً بك في لوحة تحكمك</h2>
            <p class="text-center text-green-100 mb-8 max-w-md">نظام ذكي يساعدك على إدارة كل شيء.</p>
         
        </div>


          <div class="w-full md:w-1/2 p-8 md:px-[60px]  flex flex-col overflow-auto">
    
            <div class="flex flex-col  mb-8">
                <!-- Placeholder for logo/profile picture -->
                <div class="w-[92px] h-[92px] flex  overflow-hidden">
                   <img src="/images/image-picture-landscape-1--photos-photo-landscape-picture-photography-camera-pictures--Streamline-Core.png" alt="">
                </div>
            </div>
<div class="p-[64px]">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">تسجيل الدخول</h2>
            <p class=" text-gray-600 mb-8">سجل الدخول للوصول إلى لوحة التحكم الخاصة بك</p>

            <!-- Display validation errors -->
      {{-- @if ($errors->has('email'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4" role="alert">
        <strong class="font-bold">خطأ!</strong>
        <span class="block sm:inline">{{ $errors->first('email') }}</span>
    </div>
@endif --}}
    <div class="text-red-600 mt-4" x-html="errorMessages"></div>


            <!-- Login Form -->
            <form action="{{ route('admin.login.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-[20px] font-bold text-[#212121] mb-2">البريد الإلكتروني</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.5a2.5 2.5 0 00-5 0V12"></path></svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                               class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                               placeholder="example@gmail.com" required autofocus>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block  font-[20px] font-bold text-[#212121] mb-2">كلمة المرور</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password"
                               class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                               placeholder="أدخل كلمة المرور" required>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                        <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="remember_me" class="mr-2 block text-sm text-gray-900">تذكرني</label>
                    </div>
                    <a href="#" class="text-[16px] text-[#185D31] hover:text-green-500">نسيت كلمة المرور؟</a>
                
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-lg font-medium text-white bg-[#185D31] hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        تسجيل الدخول
                    </button>
                </div>
            </form>
</div>
        </div>
    </div>




    <script>
function adminLogin() {
    return {
        errorMessages: '',
        async submit(e) {
            this.errorMessages = '';
            const form = e.target;
            const formData = new FormData(form);

            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                window.location.href = data.redirect;
            } else {
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        this.errorMessages += `<p>${messages.join(', ')}</p>`;
                    }
                }
            }
        }
    }
}
</script>