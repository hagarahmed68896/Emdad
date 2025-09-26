@extends('layouts.admin')
@section('page_title', __('messages.contact_settings'))

@section('content')
<div class="p-6 bg-gray-50 h-screen overflow-y-auto"> 
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">{{ __('messages.contact_settings') }}</h2>

    {{-- Success/Failure Message --}}
    @if(request('message'))
        <div class="p-4 mb-4 text-green-800 bg-green-100 border border-green-300 rounded-lg shadow">
            {{ request('message') }}
        </div>
    @endif

    <form action="{{ route('admin.contact.settings.store') }}" method="POST" class="space-y-4">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-2">{{ __('messages.address') }}</label>
                <input type="text" name="address" 
                       value="{{ old('address', $setting->address ?? '') }}" 
                       class="w-full rounded-lg border p-2">
            </div>

            <div>
                <label class="block mb-2">{{ __('messages.phone_number') }}</label>
                <input type="text" name="phone" 
                       value="{{ old('phone', $setting->phone ?? '') }}" 
                       class="w-full rounded-lg border p-2">
            </div>
        </div>

        <div>
            <label class="block mb-2">{{ __('messages.email') }}</label>
            <input type="email" name="email" 
                   value="{{ old('email', $setting->email ?? '') }}" 
                   class="w-full rounded-lg border p-2">
        </div>

        <div 
            x-data='{
                newPlatform: "",
                newUrl: "",
                socials: @json($setting->social_links ?? []),
                addSocial() {
                    if (!this.newPlatform || !this.newUrl) return;
                    this.socials = { ...this.socials, [this.newPlatform]: this.newUrl };
                    this.resetInputs();
                },
                resetInputs() {
                    this.newPlatform = "";
                    this.newUrl = "";
                },
                removeSocial(platform) {
                    const { [platform]: _, ...rest } = this.socials;
                    this.socials = rest;
                }
            }'
        >
            <label class="block mb-1 font-bold">{{ __('messages.social_links') }}</label>

            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
                <select x-model="newPlatform"
                        class="border p-2 rounded-xl w-full sm:w-auto">
                    <option value="">{{ __('messages.select_platform') }}</option>
                    <option value="facebook">Facebook</option>
                    <option value="twitter">Twitter</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="youtube">YouTube</option>
                </select>

                <input type="url"
                       x-model="newUrl"
                       :placeholder="__('messages.enter_url_here')"
                       class="border p-2 rounded-xl flex-1">

                <button type="button"
                        @click="addSocial"
                        class="bg-[#185D31] text-white px-4 py-2 rounded-xl w-full sm:w-auto">
                    {{ __('messages.add') }}
                </button>
            </div>

            <div class="flex flex-col gap-2" x-show="Object.keys(socials).length > 0">
                <template x-for="(url, platform) in socials" :key="platform">
                    <div class="bg-gray-100 rounded-xl px-4 py-2 flex items-center justify-between gap-3">
                        <input type="hidden" :name="`social_links[${platform}]`" :value="url">
                        <div class="flex items-center gap-2">
                            <span class="font-bold capitalize" x-text="platform"></span>
                            <a :href="url" target="_blank" class="text-blue-600 underline text-sm" x-text="url"></a>
                        </div>
                        <button type="button"
                                @click="removeSocial(platform)"
                                class="text-red-500 text-sm font-bold">
                            ✕
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div>
            <label class="block mb-2">{{ __('messages.copyrights') }}</label>
            <input type="text" name="copyrights" 
                   value="{{ old('copyrights', $setting->copyrights ?? '') }}" 
                   class="w-full border rounded-lg p-2">
        </div>

        <div class="flex gap-4 mt-6">
            <button type="submit" class="bg-[#185D31] text-white rounded-lg px-4 py-2">{{ __('messages.save') }}</button>
            <a href="{{ url()->previous() }}" class="bg-gray-300 rounded-lg px-4 py-2">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
