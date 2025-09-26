@extends('layouts.admin')

@section('page_title', __('messages.banned_users'))

@section('content')

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold">{{ __('messages.banned_users') }}</p>
    <div>
        @include('admin.total_banned_users')
    </div>

    <div x-data="{
        selectedUsers: [],
        selectAll: false,
        usersOnPage: JSON.parse('{{ $users->pluck('id')->toJson() }}'),
        init() {
            this.$watch('selectedUsers', () => {
                this.selectAll = this.selectedUsers.length === this.usersOnPage.length && this.usersOnPage.length > 0;
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedUsers = [...this.usersOnPage];
            } else {
                this.selectedUsers = [];
            }
        }
    }" class="rounded-xl shadow mx-2">

        <div class="bg-white p-6 rounded-xl">

            <div x-show="selectedUsers.length > 0" class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                        x-text="selectedUsers.length + ' {{ __('messages.selected') }}'"></span>

                    <button type="button" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                    class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                            </th>
                            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.name') }}</th>
                            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.account_type') }}</th>
                            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.email') }}</th>
                            <th scope="col" class="px-6 py-3 rtl:text-right text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.phone') }}</th>
                            <th scope="col" class="px-6 py-3 text-center text-[18px] font-bold text-[#212121] uppercase tracking-wider">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                    <input type="checkbox" :value="{{ $user->id }}" x-model="selectedUsers"
                                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                    {{ $loop->iteration + $users->firstItem() - 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ $user->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                    {{ $user->account_type === 'customer' ? __('messages.customer') : __('messages.supplier') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">{{ $user->phone_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <div x-data="{ openBan: false }" class="inline-block">
                                        <button type="button" @click="openBan = true" class="text-[#185D31]">
                                            @if ($user->status === 'banned')
                                                <i class="fas fa-unlock"></i>
                                            @else
                                                <i class="fas fa-ban"></i>
                                            @endif
                                        </button>

                                        <div x-show="openBan" x-cloak x-transition.opacity
                                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div @click.away="openBan = false" class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">
                                                @if ($user->status === 'banned')
                                                    <h2 class="text-lg font-bold mb-4">{{ __('messages.confirm_unban') }}</h2>
                                                    <p class="text-gray-600 mb-6">{{ __('messages.confirm_unban_text') }}</p>
                                                @else
                                                    <h2 class="text-lg font-bold mb-4">{{ __('messages.confirm_ban') }}</h2>
                                                    <p class="text-gray-600 mb-6">{{ __('messages.confirm_ban_text') }}</p>
                                                @endif

                                                <form action="{{ $user->status === 'banned' ? route('banned.unban', $user->id) : route('banned.ban', $user->id) }}" method="POST" class="flex justify-center gap-4">
                                                    @csrf
                                                    @if ($user->status === 'banned')
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 py-2 rounded-xl bg-yellow-600 text-white hover:bg-yellow-700">{{ __('messages.confirm_unban') }}</button>
                                                    @else
                                                        <button type="submit" class="px-4 py-2 rounded-xl bg-yellow-600 text-white hover:bg-yellow-700">{{ __('messages.confirm_ban') }}</button>
                                                    @endif
                                                    <button type="button" @click="openBan = false" class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">{{ __('messages.cancel') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('messages.no_users_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <nav class="flex items-center justify-between p-4 bg-[#EDEDED]" aria-label="Pagination">
            <div class="flex-1 flex justify-between items-center">
                <span class="text-sm text-gray-700 ml-4">
                    {{ $users->firstItem() }} - {{ $users->lastItem() }} {{ __('messages.of') }} {{ $users->total() }}
                </span>
                <div class="flex">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ __('messages.rows_per_page') }}:
                        <form action="{{ route('admin.users.index') }}" method="GET" class="inline-block">
                            <input type="hidden" name="status" value="{{ $statusFilter ?? '' }}">
                            <input type="hidden" name="search" value="{{ $search ?? '' }}">
                            <input type="hidden" name="account_type" value="customer">
                            <input type="hidden" name="sort" value="{{ $sortFilter ?? '' }}">
                            <select name="per_page" onchange="this.form.submit()" class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </form>
                    </span>
                    <div class="flex">
                        {!! $users->appends(request()->query())->links('pagination::tailwind') !!}
                    </div>
                </div>
            </div>
        </nav>

    </div>
</div>
@endsection
