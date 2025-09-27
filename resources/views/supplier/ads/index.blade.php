<div class="bg-white p-[64px]">
    <div class="flex flex-col md:flex-row justify-between">
        {{-- Ads Section --}}
        <h2 class="text-[40px] font-bold mb-4">{{ __('messages.myAds') }}</h2>

        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('supplier.ads.create') }}" 
               class="flex bg-[#185D31] text-white px-4 py-2 rounded-xl items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-6 rtl:ml-2 ltr:mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('messages.create_new_ad') }}
            </a>
        </div>
    </div>

    @if ($ads && $ads->count() === 0)
        <div class="flex flex-col items-center p-4">
            <img src="{{ asset('/images/Chats illustration.svg') }}" alt="">
            <p class="mt-4 text-[24px] text-[#696969]">{{ __('messages.no_ads') }}</p>
        </div>
    @elseif ($ads && $ads->count())
        <div class="py-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($ads as $ad)
                    <div id="ad-card-{{ $ad->id }}"  class="bg-white rounded-xl overflow-hidden shadow-md flex flex-col p-4">
                        
                        {{-- Image --}}
                        @if($ad->image)
                         <img src="{{ asset('storage/' . $ad->image) }}" 
     alt="{{ $ad->title }}" 
     class="w-full h-60 object-cover rounded-lg mb-3">

                        @endif

                        {{-- Title --}}
                        <h3 class="text-[24px] font-bold text-[#212121] mb-2">{{ $ad->title }}</h3>

                        {{-- Status --}}
                        <span class="py-1 w-auto rounded-full text-sm font-medium mb-3
                            @if($ad->status === 'approved') text-green-800 
                            @elseif($ad->status === 'pending') text-yellow-800 
                            @else bg-red-100 text-red-800 @endif">
                            {{ __('messages.' . $ad->status) }}
                        </span>

                        {{-- Amount --}}
                        <p class="text-[20px] font-bold text-[#185D31] mb-2">
                            ${{ $ad->amount }}
                        </p>

                        {{-- Dates --}}
                        <p class="text-sm text-gray-600 mb-4">
                            {{ \Carbon\Carbon::parse($ad->start_date)->format('Y-m-d') }}
                            →
                            {{ \Carbon\Carbon::parse($ad->end_date)->format('Y-m-d') }}
                        </p>

    <div class="mt-auto inline-flex">
    {{-- Edit Button --}}
    <a href="{{ route('supplier.ads.edit', $ad->id) }}" 
       class="flex items-center justify-center text-[#185D31] text-center py-[10px] px-2 rounded-[12px] font-medium transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>
    </a>

<div x-data="{ open: false }">
    <button @click="open = true"
            class="flex items-center justify-center text-red-700 py-[10px] px-2 rounded-[12px] font-medium">
       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
</svg>

    </button>

    <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.confirm_delete') }}</h2>
            <div class="flex justify-end space-x-3">
                <button @click="open = false" class="px-4 py-2 mx-2 rounded-lg bg-gray-200">{{ __('messages.cancel') }}</button>
                <button @click="deleteAd()" class="px-4 py-2 rounded-lg bg-red-600 text-white">{{ __('messages.delete') }}</button>
            </div>
        </div>
    </div>

    <script>
        function deleteAd() {
            fetch('{{ route('supplier.ads.destroy', $ad->id) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
            })
            .then(res => res.json())
            .then(data => {
                // Remove ad card from DOM
                document.getElementById('ad-card-' + data.ad_id)?.remove();
                open = false; // close modal
            })
            .catch(err => console.error(err));
        }
    </script>
</div>


</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
