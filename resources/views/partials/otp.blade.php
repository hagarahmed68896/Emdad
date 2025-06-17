<div x-show="showOTP" x-transition x-cloak
    class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50"
    style="backdrop-filter: blur(2px);">
    <div style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');"
        class="relative w-[90%] h-auto bg-cover bg-center flex flex-col justify-center items-center rounded-lg shadow-lg mt-[60px] p-4">
        <button @click="showOTP = false"
            class="absolute top-3 right-3 bg-white p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none z-10">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="relative bg-white w-[300px] md:w-[588px] h-[566px] p-[60px] rounded-[12px] shadow-xl overflow-y-auto">
            <h2 class="text-2xl font-bold mb-4 text-gray-800 text-center">تحقق من هويتك</h2>

            <p class="text-gray-500 text-sm mb-6 text-center">
                (سيصلك خلال <span id="timer-display">00:30</span> ثانية)
            </p>

            @foreach (['status', 'success', 'info'] as $msg)
                @if (session($msg))
                    <div class="bg-{{ $msg === 'info' ? 'blue' : 'green' }}-100 border border-{{ $msg === 'info' ? 'blue' : 'green' }}-400 text-{{ $msg === 'info' ? 'blue' : 'green' }}-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        {{ session($msg) }}
                    </div>
                @endif
            @endforeach

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('otp.verify.submit') }}" method="POST" id="otp-form">
                @csrf

                <div class="otp-input-container flex justify-center gap-2" dir="ltr">
                    @for ($i = 1; $i <= 4; $i++)
                        <input type="text" id="otp{{ $i }}" name="otp_digit[]"
                            class="otp-input w-12 h-12 text-center border rounded text-xl" maxlength="1"
                            pattern="[0-9]" inputmode="numeric" autocomplete="one-time-code" required>
                    @endfor
                    <input type="hidden" name="otp_code" id="hidden-otp-code">
                </div>

                <p class="text-gray-600 mt-4 text-center">
                    {{ __('messages.not_recieve') }} <button type="button" id="resend-otp-btn"
                        class="text-[#185D31] text-[16px] hover:underline disabled:text-gray-400 disabled:no-underline" disabled>
                        {{ __('messages.resend') }}
                    </button>
                </p>

                <p class="text-gray-600 mt-4 text-center">
                    @if (session('identifier_type') === 'email')
                        {{ __('messages.use_phone_instead') }}
                        <form action="{{ route('otp.switch.method') }}" method="POST" class="inline-block">
                            @csrf
                            <input type="hidden" name="method" value="phone">
                            <button type="submit" class="text-[#185D31] hover:underline">(رقم الهاتف)</button>
                        </form>
                    @else
                        {{ __('messages.use_email_instead') }} <form action="{{ route('otp.switch.method') }}"
                            method="POST" class="inline-block">
                            @csrf
                            <input type="hidden" name="method" value="email">
                            <button type="submit" class="text-[#185D31] hover:underline">(البريد الإلكتروني)</button>
                        </form>
                    @endif
                </p>

                <button type="submit" id="verify-button"
                    class="w-full bg-[#185D31] disabled:bg-[#EDEDED] disabled:text-[#696969] text-white font-bold py-2 px-4 rounded-lg mt-6"
                    disabled>
                    {{ __('messages.confirm') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const otpInputs = document.querySelectorAll('.otp-input');
        const hiddenOtpCode = document.getElementById('hidden-otp-code');
        const resendButton = document.getElementById('resend-otp-btn');
        const timerDisplay = document.getElementById('timer-display');
        const verifyButton = document.getElementById('verify-button');

        const RESEND_COOLDOWN_SECONDS = 30;
        let timeLeft = RESEND_COOLDOWN_SECONDS;
        let timerInterval;

        function startTimer() {
            resendButton.disabled = true;
            timerDisplay.textContent = `00:${String(timeLeft).padStart(2, '0')}`;
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = `00:${String(timeLeft).padStart(2, '0')}`;
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    resendButton.disabled = false;
                    timerDisplay.textContent = '00:00';
                    timeLeft = RESEND_COOLDOWN_SECONDS;
                }
            }, 1000);
        }

        function combineOtp() {
            let combined = '';
            otpInputs.forEach(input => combined += input.value);
            hiddenOtpCode.value = combined;
            verifyButton.disabled = combined.length !== 4;
        }

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                combineOtp();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = e.clipboardData.getData('text').trim();
                if (/^\d{6}$/.test(data)) {
                    for (let i = 0; i < 6; i++) {
                        otpInputs[i].value = data[i];
                    }
                    combineOtp();
                    otpInputs[5].focus();
                } else {
                    otpInputs.forEach(i => i.value = '');
                    combineOtp();
                }
            });
        });

        resendButton.addEventListener('click', () => {
            if (resendButton.disabled) return;
            fetch("{{ route('otp.resend') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        window.location.reload();
                        throw new Error('Failed to resend OTP.');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('OTP resent:', data);
                    startTimer();
                })
                .catch(error => {
                    console.error('Resend error:', error);
                });
        });

        // Start timer on load
        startTimer();
        combineOtp();
    });
</script>
