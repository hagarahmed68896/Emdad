@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-[32px] font-bold mb-6 text-gray-800">{{ __('messages.auto_messages') }}</h2>

<div class="flex justify-end">
  <a href="{{ route('admin.quick_replies.create') }}" 
     class="bg-[#185D31] text-white px-4 py-2 rounded-md mb-4 inline-block">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block mr-2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      {{ __('messages.add_auto_message') }}
  </a>
</div>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ __('messages.success') }}</div>
    @endif

    <div class="bg-white rounded shadow overflow-hidden">
        @foreach($quickReplies as $reply)
        <div class="p-4 border-b flex justify-between items-start">
            <div>
                <h3 class="font-bold">{{ $reply->text }}</h3>
                <p class="text-gray-600">{{ $reply->answer }}</p>
            </div>
            <div class="flex gap-2 mt-2">
                <!-- Edit Button -->
                <a href="{{ route('admin.quick_replies.edit', $reply->id) }}" 
                   class=" p-2 text-[#185D31] rounded-lg"
                   title="{{ __('messages.edit') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </a>

                <!-- Delete Button (Trigger Modal) -->
                <button type="button" 
                        onclick="openDeleteModal('{{ $reply->id }}')" 
                        class="text-red-600 p-2 rounded-lg"
                        title="{{ __('messages.delete') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                         stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                    </svg>
                </button>

                <!-- Delete Confirmation Modal -->
                <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg">
                        <h2 class="text-lg font-bold mb-4 text-gray-800">{{ __('messages.confirm_delete') }}</h2>
                        <p class="text-gray-600 mb-6">{{ __('messages.delete_warning') }}</p>

                        <div class="flex justify-end gap-3">
                            <button onclick="closeDeleteModal()" 
                                    class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">
                                {{ __('messages.cancel') }}
                            </button>

                            <!-- Form will be injected dynamically -->
                            <form id="deleteForm" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                                    {{ __('messages.yes_delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- JS to control modal -->
            <script>
                function openDeleteModal(replyId) {
                    const modal = document.getElementById('deleteModal');
                    const form = document.getElementById('deleteForm');
                    form.action = `/admin/quick_replies/${replyId}`;
                    modal.classList.remove('hidden');
                }

                function closeDeleteModal() {
                    document.getElementById('deleteModal').classList.add('hidden');
                }
            </script>
        </div>
        @endforeach
    </div>
</div>
@endsection
