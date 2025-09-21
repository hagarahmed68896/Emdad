<script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
    [x-cloak] { display: none !important; }
</style>

        <div dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
        style="font-family: 'Cairo', sans-serif;"
        class="flex flex-col rtl md:flex-row w-full h-screen bg-white overflow-hidden">
        <div class="w-full md:w-1/2 bg-[#185D31] p-8 md:p-12 flex flex-col justify-center items-center text-white overflow-auto">
                <div class="w-full max-w-md bg-white rounded-xl p-4 shadow-lg mb-4">
                <img src="/images/cd11071e9281b541cdbadadda2e9d1fcb71ad2cb.png" alt="Dashboard Preview" class="w-full h-auto rounded-lg">
            </div>
            <h2 class="text-4xl font-bold text-center mb-6">مرحباً بك في لوحة تحكمك</h2>
            <p class="text-center text-green-100 mb-8 max-w-md">نظام ذكي يساعدك على إدارة كل شيء.</p>         
        </div>



            <div class="w-full md:w-1/2 p-8 md:px-[60px]  flex flex-col overflow-auto" x-data="adminLogin()">
    
                <div class="flex flex-col  mb-8">
                    <div class="w-[92px] h-[92px] flex  overflow-hidden">
                       <img src="/images/image-picture-landscape-1--photos-photo-landscape-picture-photography-camera-pictures--Streamline-Core.png" alt="">
                    </div>
                </div>
       <div class="p-[64px]">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">تسجيل الدخول</h2>
            <p class=" text-gray-600 mb-8">سجل الدخول للوصول إلى لوحة التحكم الخاصة بك</p>

            <div class="text-red-600 mt-4" x-html="errorMessages"></div>


     <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label class="block mb-2">البريد الإلكتروني</label>
                <input type="email" x-model="form.email"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3"
                       placeholder="example@gmail.com" required>
            </div>

      <div x-data="{ show: false }" class="relative">
    <label class="block mb-2">كلمة المرور</label>
    
    <input :type="show ? 'text' : 'password'" 
           x-model="form.password"
           class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-10"
           placeholder="ادخل كلمة المرور" required>
    
    <!-- أيقونة إظهار/إخفاء -->
    <button type="button" 
            @click="show = !show"
            class="absolute inset-y-0 left-3 top-8 flex items-center px-3 text-gray-500">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
            <path x-show="!show" stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.637 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.637 0-8.573-3.007-9.963-7.178Z"/>
            <path x-show="!show" stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            
            <path x-show="show" stroke-linecap="round" stroke-linejoin="round"
                  d="M3 3l18 18M10.477 10.477A3 3 0 0 0 12 15a3 3 0 0 0 1.523-5.523M6.882 6.882C5.074 8.097 3.65 9.94 2.965 12.177c-.07.207-.07.431 0 .639C4.423 16.49 8.36 19.5 13 19.5c1.563 0 3.04-.336 4.318-.935"/>
        </svg>
    </button>
</div>


    <div class="flex justify-between items-center mt-2">
    <label class="inline-flex items-center">
        <input type="checkbox" x-model="form.remember_me" class="h-4 w-4 text-green-600">
        <span class="mr-2">تذكرني</span>
    </label>

<!-- Forgot Password Button + Modal -->
<div x-data="{ showForgotPassword: false, forgotEmail: '', forgotMessage: '' }" x-cloak class="inline">
    <!-- Button -->
    <button type="button" @click="showForgotPassword = true"
        class="text-sm text-[#185D31] hover:underline">
        نسيت كلمة المرور؟
    </button>

    <!-- Modal -->
    <div x-show="showForgotPassword" x-transition.opacity 
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div @click.away="showForgotPassword = false"
             class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">
            
            <h2 class="text-lg font-bold mb-4">إعادة تعيين كلمة المرور</h2>
            <p class="text-gray-600 mb-4">أدخل بريدك الإلكتروني لتلقي رابط إعادة التعيين.</p>
            
            <input type="email" x-model="forgotEmail"
                   placeholder="example@gmail.com"
                   class="w-full border border-gray-300 rounded-xl px-4 py-2 mb-4">

            <div class="text-green-600 text-sm mb-2" x-text="forgotMessage"></div>

            <div class="flex justify-center gap-4">
                <button type="button"
                        @click="sendResetLink()"
                        class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                    إرسال الرابط
                </button>
                <button type="button" @click="showForgotPassword = false"
                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
</div>

</div>

            <button type="submit"
                    class="w-full bg-[#185D31] text-white py-3 rounded-xl">
                 تسجيل الدخول
            </button>
        </form>
</div>
        </div>
    </div>

<script>
    function adminLogin() {
        return {
            form: {
                email: '',
                password: '',
                remember_me: false,
            },
            errorMessages: '',


            async submit() {
                this.errorMessages = '';

                const response = await fetch('{{ route('admin.login.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify(this.form)
                });

                let data = {};
                try {
                    data = await response.json();
                } catch (e) {
                    this.errorMessages = 'حدث خطأ في قراءة الرد من الخادم.';
                    return;
                }

                if (response.ok) {
                    window.location.href = data.redirect;
                } else if (data.errors) {
                    Object.values(data.errors).forEach(msgs => {
                        this.errorMessages += `<p>${msgs.join(', ')}</p>`;
                    });
                } else if (data.message) {
                    this.errorMessages = `<p>${data.message}</p>`;
                } else {
                    this.errorMessages = 'حدث خطأ غير متوقع.';
                }
            },
             // ✅ Forgot Password Logic
        async sendResetLink() {
            if (!this.forgotEmail) {
                this.forgotMessage = 'الرجاء إدخال البريد الإلكتروني.';
                return;
            }

            try {
                const response = await fetch('{{ route('admin.password.email') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ email: this.forgotEmail })
                });

                const data = await response.json();

                if (response.ok) {
                    this.forgotMessage = 'تم إرسال رابط إعادة التعيين إلى بريدك الإلكتروني.';
                } else if (data.errors) {
                    this.forgotMessage = Object.values(data.errors).flat().join(', ');
                } else if (data.message) {
                    this.forgotMessage = data.message;
                } else {
                    this.forgotMessage = 'حدث خطأ غير متوقع.';
                }
            } catch (e) {
                this.forgotMessage = 'حدث خطأ أثناء إرسال البريد الإلكتروني.';
            }
        }
        };

    }
</script>


