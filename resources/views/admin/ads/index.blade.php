@extends('layouts.admin')

@section('page_title', __('messages.manage_ads'))

@section('content')
<div 
    x-data="{}"
    class="p-6 bg-white h-screen overflow-y-auto"
>
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">{{ __('messages.manage_ads') }}</h2>

    {{-- ✅ Table --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full border text-center">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">{{ __('messages.supplier') }}</th>
                    <th class="p-3">{{ __('messages.adTitle') }}</th>
                    <th class="p-3">{{ __('messages.amount') }}</th>
                    <th class="p-3">{{ __('messages.status') }}</th>
                    <th class="p-3">{{ __('messages.dates') }}</th>
                    <th class="p-3">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ads as $ad)
                <tr class="border-b">
                    <td class="p-3">{{ $loop->iteration + ($ads->currentPage()-1) * $ads->perPage() }}</td>
                    <td class="p-3">{{ $ad->supplier->full_name }}</td>
                    <td class="p-3">{{ $ad->title }}</td>
                    <td class="p-3">{{ number_format($ad->amount, 2) }} <span class="text-gray-500">{{ __('messages.currency') }}</span></td>
                    <td class="p-3">
                        @if($ad->status === 'pending')
                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-medium">
                                {{ __('messages.Pending') }}
                            </span>
                        @elseif($ad->status === 'approved')
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-medium">
                                {{ __('messages.approved') }}
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-medium">
                                {{ __('messages.rejected') }}
                            </span>
                        @endif
                    </td>
                    <td class="p-3">{{ $ad->start_date }} → {{ $ad->end_date }}</td>
                    <td class="p-3">
                        @if($ad->status === 'pending')
                            <div class="flex justify-center gap-2">
                                {{-- Approve --}}
                                <form action="{{ route('admin.ads.approve', $ad->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl bg-green-600 text-white hover:bg-green-700 text-sm">
                                        {{ __('messages.approve') }}
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <form action="{{ route('admin.ads.reject', $ad->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700 text-sm">
                                        {{ __('messages.reject') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">{{ __('messages.no_actions') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-3 text-gray-500">{{ __('messages.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ Pagination --}}
    <nav class="flex items-center justify-between p-4 bg-[#EDEDED]">
        <div class="flex-1 flex justify-between items-center">
            <span class="text-sm text-gray-700 ml-4">
                {{ $ads->firstItem() }} - {{ $ads->lastItem() }} {{ __('messages.of') }} {{ $ads->total() }}
            </span>
            <div class="flex">
                {!! $ads->appends(request()->query())->links('pagination::tailwind') !!}
            </div>
        </div>
    </nav>
</div>
@endsection
