<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex flex-wrap">
                    <a href="{{ route('categories') }}" class="w-full cursor-pointer md:w-1/2 xl:w-1/3 p-6">
                        <!--Metric Card-->
                        <div
                            class="bg-gradient-to-b from-blue-200 to-blue-100 border-b-4 border-blue-500 rounded-lg shadow-xl p-5">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded-full p-5 bg-blue-600"><i
                                            class="fas fa-server fa-2x fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-600">Total Categories</h5>
                                    <h3 class="font-bold text-3xl">{{ $cCount }}</h3>
                                </div>
                            </div>
                        </div>
                        <!--/Metric Card-->
                    </a>
                    <a href="{{ route('questions') }}" class="w-full cursor-pointer md:w-1/2 xl:w-1/3 p-6">
                        <!--Metric Card-->
                        <div
                            class="bg-gradient-to-b from-green-200 to-green-100 border-b-4 border-green-600 rounded-lg shadow-xl p-5">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded-full p-5 bg-green-600"><i
                                            class="fa fa-wallet fa-2x fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-600">Total Questions</h5>
                                    <h3 class="font-bold text-3xl">{{ $qCount }} </h3>
                                </div>
                            </div>
                        </div>
                        <!--/Metric Card-->
                    </a>
                    <a href="{{ route('dashboard') }}" class="w-full cursor-pointer md:w-1/2 xl:w-1/3 p-6">
                        <!--Metric Card-->
                        <div
                            class="bg-gradient-to-b from-pink-200 to-pink-100 border-b-4 border-pink-500 rounded-lg shadow-xl p-5">
                            <div class="flex flex-row items-center">
                                <div class="flex-shrink pr-4">
                                    <div class="rounded-full p-5 bg-pink-600"><i
                                            class="fas fa-users fa-2x fa-inverse"></i></div>
                                </div>
                                <div class="flex-1 text-right md:text-center">
                                    <h5 class="font-bold uppercase text-gray-600">Total Users</h5>
                                    <h3 class="font-bold text-3xl">{{ $uCount }}
                                        {{-- <span class="text-pink-500">
                                    <i class="fas fa-exchange-alt"></i>
                                </span> --}}
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <!--/Metric Card-->
                    </a>
                </div>
                {{-- <div class="header-container container flex justify-between pl-6 pr-6 pt-4 pb-4">
                <div class="logo-wrapper flex justify-center items-center">
                    <h1><a href="https://youtoocanrun.com" target="_blank"><img style="width:100px" src="{{url('/images/ytcrlogo.png')}}" alt=""></a></h1>
                </div>
                <div class="header-right-banner-wrapper">
                    <div class="logo-wrapper" style="color:#CF2121;font-size:14px !important;font-style:italic">
                        <h3 style="text-align: right; padding-right: inherit; color: #CF2121; font-style: italic; margin-top: 0; margin-bottom: 0">We Promote Running </h3>
                        <h3 style="text-align: right; padding-right: inherit; color: #CF2121; font-style: italic; margin-top: 0; margin-bottom: 0">for Good Health</h3>
                    </div>
                </div>	
            </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
