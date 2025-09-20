@extends('layouts.app')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/preline@1.9.0/dist/preline.js"></script>
    <style>
        .arrow-icon {
            transition: transform 0.3s ease;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #333;
        }

        .faq-item.open .arrow-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease-out, padding-top 0.3s ease-out;
        }

        .faq-item.open .faq-answer {
            max-height: 500px;
        }
    </style>

    <div class="container flex bg-white p-4">
        <div class="row mb-2 mt-4">
            <!-- العمود الأيسر -->
            <div class="col-lg-6 p-2">
                <div class="flex-grow-2 mt-1">
                    <h1 class="text-[#212121] text-[32px] mb-3 text-right font-bold">الأسئلة الشائعة</h1>
                    <p class="text-[#767676] text-lg text-right">هل لديك سؤال؟</p>
                    <p class="text-[#767676] text-base text-right">اعثر على إجابات للاستفسارات الشائعة حول منتجاتنا
                        وخدماتنا</p>
                </div>

                <div class="flex justify-center items-center relative overflow-hidden mt-4">
                    <img src="{{ asset('images/image.png') }}" alt="Man looking confused holding a phone"
                         class="max-w-[80%] h-auto block rounded-md object-contain">
                    <img class="absolute rounded-full w-[80px] h-[64px] flex justify-center items-center top-1/5 left-[72%]"
                         src="{{ asset('images/Group (7).svg') }}" alt="">
                    <img class="absolute rounded-full w-[80px] h-[64px] flex justify-center items-center top-1/4 right-[55%]"
                         src="{{ asset('images/Group (6).svg') }}" alt="">
                </div>

           <!-- زر تواصل معنا + المودال -->
<div x-data="{ showContactUs: false }" @close-contact.window="showContactUs = false" class="mt-4">
    <button @click="showContactUs = true"
            class="bg-[#185D31] w-full text-white px-4 py-2 rounded-xl text-lg font-semibold transition-colors duration-200 hover:bg-[#154a29]">
        {{ __('messages.contact_us') }}
    </button>

    <!-- Contact Us Modal -->
    <div x-show="showContactUs" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         style="backdrop-filter: blur(2px);" @click.self="showContactUs = false">

        <div class="bg-white p-8 rounded-lg shadow-xl w-[588px]">
            <h2 class="text-[32px] text-[#212121] font-bold mb-4">{{ __('messages.contactUs') }}</h2>
            <p class="mb-6">{{ __('messages.contactDescription') }}</p>

            <!-- success message (AJAX) -->
            <div id="contactSuccess" class="hidden bg-green-100 text-green-700 p-3 rounded-md mb-4"></div>

            <form id="contactForm" action="{{ route('contact.store') }}" method="POST" novalidate>
                @csrf

                <div class="mb-3">
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.name') }}</label>
                    <div class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="text" name="name" required
                               placeholder="{{ __('messages.nameMSG') }}"
                               class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none">
                    </div>
                    <p id="error-name" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <div class="mb-3">
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.email') }}</label>
                    <div class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <input type="email" name="email" required
                               placeholder="{{ __('messages.mailMessage') }}"
                               class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none">
                    </div>
                    <p id="error-email" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <div class="mb-3">
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.expType') }}</label>
                    <select name="type"
                            class="w-full px-3 py-2 border border-[#767676] rounded-[12px] focus:outline-none">
                        <option value="">-- اختر النوع --</option>
                        <option value="استفسار عام">استفسار عام</option>
                        <option value="استفسار عن منتج">استفسار عن منتج</option>
                        <option value="مشكلة في الطلب أو التوصيل">مشكلة في الطلب أو التوصيل</option>
                        <option value="أخرى">أخرى</option>
                    </select>
                    <p id="error-type" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <div class="mb-3">
                    <label class="block font-bold text-[20px] text-[#212121]">{{ __('messages.msg') }}</label>
                    <div class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                        <textarea name="message" rows="5" required
                                  placeholder="{{ __('messages.writeQuestion') }}"
                                  class="block w-full px-3 py-2 border-none focus:outline-none"></textarea>
                    </div>
                    <p id="error-message" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>

                <button type="submit" id="contactSubmit"
                        class="w-full bg-[#185D31] text-white p-[12px] h-[48px] rounded-[12px] hover:bg-green-800">
                    {{ __('messages.send') }}
                </button>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('contactForm');
    const successBox = document.getElementById('contactSuccess');
    const submitBtn = document.getElementById('contactSubmit');

    function clearErrors() {
        ['name','email','type','message'].forEach(k => {
            const el = document.getElementById('error-' + k);
            if (el) { el.textContent = ''; el.classList.add('hidden'); }
        });
    }

    function showErrors(errors) {
        for (const key in errors) {
            const el = document.getElementById('error-' + key);
            if (el) {
                el.textContent = errors[key].join(' ');
                el.classList.remove('hidden');
            }
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();
        successBox.classList.add('hidden');
        submitBtn.disabled = true;

        const token = form.querySelector('input[name="_token"]').value;
        const url = form.getAttribute('action');
        const formData = new FormData(form);

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                // validation errors (422) or other
                if (data.errors) {
                    showErrors(data.errors);
                } else if (data.message) {
                    successBox.textContent = data.message;
                    successBox.classList.remove('hidden');
                } else {
                    successBox.textContent = 'حدث خطأ غير متوقع. حاول مرة أخرى.';
                    successBox.classList.remove('hidden');
                }
            } else {
                // success
                successBox.textContent = data.message || 'تم الإرسال';
                successBox.classList.remove('hidden');
                form.reset();

                // dispatch event to close modal after short delay
                setTimeout(() => {
                    window.dispatchEvent(new Event('close-contact'));
                }, 1000);
            }
        } catch (err) {
            console.error(err);
            successBox.textContent = 'خطأ في الشبكة. تأكد من اتصالك وحاول مرة أخرى.';
            successBox.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
        }
    });
});
</script>

            </div>

            <!-- العمود الأيمن (الأسئلة من قاعدة البيانات) -->
            <div class="col-lg-6 p-2">
                @forelse($faqs as $faq)
                    <div class="faq-item border-b border-gray-200 rounded-[20px] bg-[#F8F9FA] p-[24px] mb-3">
                        <div class="faq-question flex justify-between items-center font-semibold text-gray-800 cursor-pointer text-lg">
                            <span class="flex-grow text-right text-[18px] leading-[150%] font-bold">
                                {{ $faq->question }}
                            </span>
                            <span class="arrow-icon ml-2"></span>
                        </div>
                        <div class="faq-answer pt-2 text-gray-700 leading-relaxed text-base">
                            <p>{{ $faq->answer }}</p>
                            {{-- <p class="mt-2 text-sm text-gray-500">
                                الفئة: 
                                {{ $faq->user_type === 'customer' ? 'عميل' : ($faq->user_type === 'supplier' ? 'مورد' : $faq->user_type) }}
                            </p> --}}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center">لا توجد أسئلة شائعة حالياً</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const faqItem = question.closest('.faq-item');
                    faqItem.classList.toggle('open');
                });
            });
        });
    </script>
@endsection
