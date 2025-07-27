@extends('layouts.admin')

@section('page_title', $user->full_name)

@section('content')
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
    <div class="bg-white rounded-xl shadow p-6 mx-auto">

        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">{{ $user->full_name }}</h2>
        </div>

        <!-- التبويبات -->
        <div class="flex mb-6">
            @php
                $tabs = [
                    'personal' => 'البيانات الشخصية',
                    'orders' => 'الطلبات',
                    'invoices' => 'الفواتير',
                    'reviews' => 'التقييمات',
                ];
                $activeTab = request('tab', 'personal');
            @endphp

            @foreach ($tabs as $key => $label)
                <a href="{{ route('admin.users.show', [$user->id, 'tab' => $key]) }}"
                   class="px-4 py-2 rounded-xl mx-1 {{ $activeTab == $key ? 'bg-[#185D31] text-white' : 'bg-gray-200 text-[#212121]' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- محتوى التبويب النشط -->
        @if ($activeTab == 'personal')   
            <div class="flex flex-col md:flex-row items-start gap-8">
                {{-- {{ dd($user->profile_picture) }} --}}

                <div>
           <img 
    src="{{ $user->profile_picture && file_exists(public_path('storage/' . $user->profile_picture)) 
        ? asset('storage/' . $user->profile_picture) 
        : asset('images/Unknown_person.jpg') }}" 
    class="w-32 h-32 rounded-full object-cover">

                </div>
                <div class="flex-1 mx-2">
                    <div class="mb-4 flex">
                  <img class="mx-2" src="{{asset('/images/interface-user-circle--circle-geometric-human-person-single-user--Streamline-Core.svg')}}" alt="">   
                     <strong class="mx-2">الاسم الكامل</strong> <span class="text-[#696969]">{{ $user->full_name }}</span>
                    </div>
                    <div class="mb-4 flex">
                      <img class="mx-2" src="{{asset('/images/mail-send-envelope--envelope-email-message-unopened-sealed-close--Streamline-Core.svg')}}" alt="">  
                         <strong class="mx-2">البريد الإلكتروني</strong> <span class="text-[#696969]">{{ $user->email }}</span>
                    </div>
                    <div class="mb-4 flex">
                  <img class="mx-2" src="{{asset('/images/phone-telephone--android-phone-mobile-device-smartphone-iphone--Streamline-Core.svg')}}" alt="">      
                     <strong class="mx-2">رقم الهاتف</strong> <span class="text-[#696969]">{{ $user->phone_number ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-4 flex">
                    <img class="mx-2" src="{{asset('/images/travel-map-location-pin--navigation-map-maps-pin-gps-location--Streamline-Core.svg')}}" alt="">      
                     <strong class="mx-2">العنوان</strong> <span class="text-[#696969]">{{ $user->address ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-4 flex">
                    <img class="mx-2" src="{{asset('/images/shopping-cart-3--shopping-cart-checkout--Streamline-Core.svg')}}" alt="">    
                       <strong class="mx-2">عدد الطلبات</strong> <span class="text-[#696969]">{{ $orders->count() }}</span>
                    </div>
                    <div class="mb-4 flex">
                          <img class="mx-2" src="{{asset('/images/money-graph-bar-increase--up-product-performance-increase-arrow-graph-business-chart--Streamline-Core.svg')}}" alt=""> 
                        <strong class="mx-2">الحالة</strong>
                        @if ($user->status === 'active')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">نشط</span>
                        @elseif ($user->status === 'inactive')
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full">غير نشط</span>
                        @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full">محظور</span>
                        @endif
                    </div>
                    <div class="mb-4 flex">
                      <img class="mx-2" src="{{asset('/images/Calender.svg')}}" alt="">   
                        <strong class="mx-2">التاريخ</strong> <span class="text-[#696969]">{{ $user->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>

            </div>



     @elseif ($activeTab == 'orders')
    <h3 class="text-xl font-bold mb-4">الطلبات</h3>
    @include('admin.users.order')



        @elseif ($activeTab == 'invoices')
            <h3 class="text-xl font-bold mb-4">الفواتير</h3>
            @include('admin.users.invoices')

            {{-- <div class="overflow-x-auto rounded-t-xl">
                <table class="min-w-full
            {{-- @forelse ($invoices as $invoice)
                <div class="mb-2 p-4 border rounded">فاتورة #{{ $invoice->id }}</div>
            @empty
                <p>لا توجد فواتير</p>
            @endforelse --}}




        @elseif ($activeTab == 'reviews')
            <h3 class="text-xl font-bold mb-4">التقييمات</h3>
            @include('admin.users.reviews')
        @endif
    </div>
</main>
@endsection
