<script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

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

            <div>
                <label class="block mb-2">كلمة المرور</label>
                <input type="password" x-model="form.password"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3"
                       placeholder="ادخل كلمة المرور" required>
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" x-model="form.remember_me" class="h-4 w-4 text-green-600">
                    <span class="ml-2">تذكرني</span>
                </label>
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
            }
        };
    }
</script>


