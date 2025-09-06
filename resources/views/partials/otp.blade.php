<div 
    x-show="showOTP" 
    x-transition 
    x-cloak
    class="fixed inset-0 z-[50] flex items-center justify-center bg-black bg-opacity-50"
    style="backdrop-filter: blur(2px);"
    x-data="otpComponent()"
    x-init="
        $watch('showOTP', value => {
            if (value) startTimer();
            else clearInterval(interval);
        })
    "
>
    <div style="background-image: url('{{ asset('images/4d2a165c129977b25a433b916ddfa33f089dcf9f.jpg') }}');"
        class="relative bg-cover bg-center flex flex-col justify-center items-center p-[24px] rounded-lg shadow-lg
               w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl xl:max-w-2xl h-auto mx-auto my-auto">

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
                (سيصلك خلال <span x-text="formattedTimer"></span> ثانية)
            </p>

            @foreach (['status', 'success', 'info'] as $msg)
                @if (session($msg))
                    <div class="bg-{{ $msg === 'info' ? 'blue' : 'green' }}-100 border border-{{ $msg === 'info' ? 'blue' : 'green' }}-400 text-{{ $msg === 'info' ? 'blue' : 'green' }}-700 px-4 py-3 rounded relative mb-4">
                        {{ session($msg) }}
                    </div>
                @endif
            @endforeach

            <div x-show="errorMessage" x-cloak class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <span x-text="errorMessage"></span>
            </div>

            <form @submit.prevent="verifyOtp">
                @csrf
                <div class="flex justify-center gap-2 mb-4" dir="ltr">
                    <template x-for="i in 4" :key="i">
                        <input 
                            type="text"
                            maxlength="1"
                            class="otp-input w-12 h-12 text-center border rounded text-xl"
                            x-model="digits[i-1]"
                            @input="handleInput($event, i)"
                            @keydown.backspace="handleBackspace($event, i)"
                        >
                    </template>
                    <input type="hidden" name="otp" :value="digits.join('')">
                </div>

                <p class="text-gray-600 text-center">
                    {{ __('messages.not_recieve') }}
                    <button 
                        type="button"
                        class="text-[#185D31] text-[16px] hover:underline disabled:text-gray-400 disabled:no-underline"
                        @click="resendOTP"
                        :disabled="timer > 0"
                    >
                        {{ __('messages.resend') }}
                    </button>
                </p>

                <button 
                    type="submit"
                    class="w-full bg-[#185D31] disabled:bg-[#EDEDED] disabled:text-[#696969] text-white font-bold py-2 px-4 rounded-lg mt-6"
                    :disabled="digits.join('').length !== 4"
                >
                    {{ __('messages.confirm') }}
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function otpComponent() {
    return {
        digits: ['', '', '', ''],
        timer: 30,
        interval: null,
        errorMessage: '', // ✅ New variable for error messages

        get formattedTimer() {
            return `00:${String(this.timer).padStart(2, '0')}`;
        },

        startTimer() {
            this.timer = 30;
            clearInterval(this.interval);
            this.interval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    clearInterval(this.interval);
                }
            }, 1000);
        },

        handleInput(e, i) {
            this.errorMessage = ''; // Clear error on new input
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            this.digits[i-1] = e.target.value;
            if (e.target.value && i < 4) {
                e.target.nextElementSibling.focus();
            }
        },

        handleBackspace(e, i) {
            this.errorMessage = ''; // Clear error on backspace
            if (!this.digits[i-1] && i > 1) {
                e.preventDefault();
                e.target.previousElementSibling.focus();
            }
        },

        // ✅ New method to handle OTP verification with AJAX
        verifyOtp() {
            this.errorMessage = ''; // Clear any existing error message

            fetch("{{ route('verifyOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    otp: this.digits.join('')
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(data => {
                if (data.status === 200 && data.body.success) {
                    // Success: Redirect to the home page
                    window.location.href = data.body.redirect;
                } else {
                    // Error: Display the message and keep the modal open
                    this.errorMessage = data.body.message || 'An unknown error occurred.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.errorMessage = 'An error occurred while verifying the OTP.';
            });
        },

        resendOTP() {
            this.errorMessage = ''; // Clear error message on resend
            fetch("{{ route('sendOtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                console.log('Resent:', data);
                this.digits = ['', '', '', ''];
                this.startTimer();
            });
        }
    }
}
</script>