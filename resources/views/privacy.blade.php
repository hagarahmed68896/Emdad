      @extends('layouts.app')

      @section('content')
          <div class="container py-5">
              <h2 class="mb-4 text-[#212121] font-bold text-[32px]">{{__('messages.privacy_policy')}}</h2>

              <div class="row">
                  <div class="col-lg-5">
                      <div class="bg-white p-4 rounded-[20px] shadow-sm" x-data="{ selectedItem: 1 }">
                          <form class="mb-3">
                              <div
                                  class="flex items-center mt-2 border-[1px] bg-[#F8F9FA]  rounded-[12px] overflow-hidden">

                                  <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
                                      alt="Search Icon" class="h-[16px] w-[16px] object-cover text-[#767676] mr-6">
                                  <input type="text"
                                      class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none 
                                      focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-[#F8F9FA]"
                                      placeholder="    {{ __('messages.Search') }}">
                <div>
                    <button type="submit"
                        class="bg-[#185D31] w-[61px] h-[32px] text-white rounded-[12px] pb-1 mx-3 text-sm">
                        {{ __('messages.Search') }}
                    </button>
                </div>
                              </div>
                              
                          </form>

                          @for ($i = 1; $i <= 5; $i++)
                              <div class="mb-2">
                                  <a href="#section{{ $i }}" @click="selectedItem = {{ $i }}"
                                      :class="{
                                          'bg-[#185D31] text-white': selectedItem === {{ $i }},
                                          'bg-white text-black border border-[#185D31]': selectedItem !==
                                              {{ $i }}
                                      }"
                                      class="
                       rounded-[20px] h-[60px] w-100 pt-3 fw-bold pb-2 text-[18px]
                       flex p-4 items-center cursor-pointer no-underline
                       transition-colors duration-100 ease-in-out
                   ">
                                    البند .{{ $i }}
                                  </a>
                              </div>
                          @endfor
                      </div>
                  </div>


                  <div class="col-lg-7 mb-4 bg-[#F8F9FA] rounded">
                      <div data-bs-spy="scroll" data-bs-target="#list-example" data-bs-smooth-scroll="true"
                          class="scrollspy-example  p-4" tabindex="0">
                          @for ($i = 1; $i <= 5; $i++)
                              <div class="mb-4">
                                  <h5 class="fw-bold">  البند .{{ $i }}</h5>
                                  <p style="line-height: 1.8; color: #333;">
                                      لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور
                                      إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور
                                      أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا
                                      أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور أديبيسينغ إيليت، سيد دو إيوسمود تيمبور
                                      إنسيديدونت ات لابوري ات دولوري ماجنا أليكوا.لوريم ابسوم دولور سيت أميت، كونسيكتيتور
                                      أديبيسينغ إيليت، سيد دو إيوسمود تيمبور إنسيديدونت ات لابوري ات دولوري ماجنا
                                      أليكوا.لوريم ابسوم دولور سيت
                                  </p>
                              </div>
                          @endfor
                      </div>
                  </div>

              </div>
          </div>
      @endsection
