@extends('layouts.admin')

@section('page_title', 'المحظورون')

@section('content')

    <div class="p-6 overflow-y-auto">

        <p class="text-[32px] font-bold">المحظورون</p>
        <div>
            @include('admin.total_banned_users')
        </div>

        {{-- MAIN ALPINE.JS SCOPE: This div now wraps the entire interactive section (filter/action bar + table) --}}
        <div x-data="{
            selectedUsers: [], // Array to hold IDs of selected users
            selectAll: false, // State for the master select all checkbox
            // Get IDs of all users currently displayed on this page. Important: parse as JSON.
            usersOnPage: JSON.parse('{{ $users->pluck('id')->toJson() }}'),
        
            // Initialize function runs when component is initialized
            init() {
                // Watch for changes in selectedUsers and update selectAll accordingly
                this.$watch('selectedUsers', () => {
                    // If all users on the page are selected AND there are users on the page, set selectAll to true
                    this.selectAll = this.selectedUsers.length === this.usersOnPage.length && this.usersOnPage.length > 0;
                });
            },
        
            // Toggles all checkboxes on or off based on `selectAll` state
            toggleSelectAll() {
                if (this.selectAll) {
                    this.selectedUsers = [...this.usersOnPage]; // Select all users on the current page
                } else {
                    this.selectedUsers = []; // Deselect all
                }
            }
        }" class="rounded-xl shadow mx-2">

            <div class="bg-white p-6 rounded-xl">

                {{-- Action Bar: Shown when one or more users are selected --}}
                <div x-show="selectedUsers.length > 0" class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                            x-text="selectedUsers.length + ' محدد'"></span>

                        {{-- Placeholder for view icon (if needed for bulk view) --}}
                        <button type="button" class="p-2 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-700">
                            <i class="fas fa-eye"></i>
                        </button>


                    </div>

                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                {{-- Master Checkbox TH: Increased padding and centered --}}
                                <th scope="col"
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()"
                                        class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                                </th>
                                {{-- Standardized padding and rtl:text-right for content THs --}}
                                <th scope="col"
                                    class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
                                    الاسم
                                </th>
                                  <th scope="col"
                                    class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
                                 النوع (عميل/مورد)
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
                                    البريد الإلكتروني
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 rtl:text-right text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
                                    رقم الهاتف
                                </th>

                                {{-- Actions TH: Centered --}}
                                <th scope="col"
                                    class="px-6 py-3 text-center text-[18px] font-bold font-medium text-[#212121] uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr>
                                    {{-- Individual Checkbox TD: Consistent padding and centered --}}
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                        <input type="checkbox" :value="{{ $user->id }}" x-model="selectedUsers"
                                            class="ml-1 h-4 w-4 text-[#185D31] bg-[#185D31] focus:ring-[#185D31] accent-[#185D31] border-[#185D31] rounded">
                                        </th>
                                    </td>
                                    {{-- Standardized padding and rtl:text-right for content TDs --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                        {{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                        {{ $user->full_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                        {{ $user->account_type === 'customer' ? 'عميل' : 'مورد' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 rtl:text-right">
                                        {{ $user->phone_number ?? 'N/A' }}</td>
                

                                            <!-- Ban button with Alpine.js modal -->
  
                                            <!-- Ban / Unban button with Alpine.js modal -->
   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">                                            <div x-data="{ openBan: false }" class="inline-block">
    <button type="button"
            @click="openBan = true"
            class="text-[#185D31]">
        @if ($user->status === 'banned')
            <i class="fas fa-unlock"></i>
        @else
            <i class="fas fa-ban"></i>
        @endif
    </button>

    <!-- Ban/Unban Confirmation Modal -->
    <div x-show="openBan" x-cloak
         x-transition.opacity
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div @click.away="openBan = false"
             class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

            @if ($user->status === 'banned')
                <h2 class="text-lg font-bold mb-4">تأكيد إلغاء الحظر</h2>
                <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد إلغاء حظر هذا المستخدم؟</p>
            @else
                <h2 class="text-lg font-bold mb-4">تأكيد الحظر</h2>
                <p class="text-gray-600 mb-6">هل أنت متأكد أنك تريد حظر هذا المستخدم؟</p>
            @endif

            <form action="{{ route('admin.users.toggle-ban', $user->id) }}" method="POST" class="flex justify-center gap-4">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-yellow-600 text-white hover:bg-yellow-700">
                    @if ($user->status === 'banned')
                        تأكيد إلغاء الحظر
                    @else
                        تأكيد الحظر
                    @endif
                </button>
                <button type="button"
                        @click="openBan = false"
                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                    إلغاء
                </button>
            </form>
        </div>
    </div>
</div>
</td>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد
                                        بيانات للمستخدمين.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <nav class="flex items-center justify-between p-4 bg-[#EDEDED]" aria-label="Pagination">
                <div class="flex-1 flex justify-between  items-center">
                    <span class="text-sm text-gray-700 ml-4">
                        {{ $users->firstItem() }} - {{ $users->lastItem() }} من {{ $users->total() }}
                    </span>
                    <div class="flex">
                        <span class="text-sm text-gray-700 ml-4">
                            الصفوف لكل صفحة:
                            <form action="{{ route('admin.users.index') }}" method="GET" class="inline-block">
                                <input type="hidden" name="status" value="{{ $statusFilter ?? '' }}">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                <input type="hidden" name="account_type" value="customer">
                                <input type="hidden" name="sort" value="{{ $sortFilter ?? '' }}">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                        </span>

                        {{-- Pagination Links --}}
                        <div class="flex">
                            {{-- Generate pagination links --}}
                            {!! $users->appends(request()->query())->links('pagination::tailwind') !!}
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
@endsection
