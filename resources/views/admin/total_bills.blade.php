  <main class="flex-1 overflow-x-hidden overflow-y-auto p-2">
            <!-- Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Users -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $totalInvoices }}</p>
                        <p class="text-gray-500 text-sm">إجمالي الفواتير</p>
                        <p class="text-[#185D31] text-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>

                            0.43%
                        </p>
                    </div>
                    <img src="{{asset('images/Growth.svg')}}" alt="">
                </div>
                <!-- Card 2: Total Customers -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $paidInvoices }}</p>

                        <p class="text-gray-500 text-sm">إجمالي المدفوع</p>
                        <p class="text-[#185D31] text-sm flex items-center">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>

                            0.43%
                        </p>
                    </div>
                    <img src="{{asset('images/Growth.svg')}}" alt="">
                </div>
                <!-- Card 3: Total Suppliers -->
                <div class="bg-white rounded-xl shadow p-6 flex items-center justify-between">
                    <div>
                        <p class="text-3xl font-bold text-gray-800">{{ $unpaidInvoices }}</p>

                        <p class="text-gray-500 text-sm">إجمالي الغير مدفوع</p>
                        <p class="text-[#C62525] text-sm flex items-center">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6 9 12.75l4.286-4.286a11.948 11.948 0 0 1 4.306 6.43l.776 2.898m0 0 3.182-5.511m-3.182 5.51-5.511-3.181" />
</svg>


                            0.43%
                        </p>
                    </div>
                    <img src="{{asset('images/Growth (1).svg')}}" alt="">
                </div>

            </div>

        </main>