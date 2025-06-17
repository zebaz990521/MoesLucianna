<x-app-layout>
  {{--   <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> --}}
    <x-slot:title>
        Dashboard
    </x-slot>



<!-- ========== Header ========== -->
<x-slot:header>
    @include('partials.header')
</x-slot>

<!-- ========== App Menu ========== -->
<x-slot:menu>
    @include('partials.menu')
</x-slot>

            <!-- ============================================================== -->
            <!-- Content Page -->
            <!-- ============================================================== -->
            <!-- Left Sidebar End -->
            <!-- Vertical Overlay-->
            <div class="vertical-overlay"></div>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            {{-- <div class="main-content"> --}}

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0">Starter Dashboard</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                            <li class="breadcrumb-item active">Starter</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                <x-slot:footer>
                    @include('partials.footer')
                </x-slot>

         {{--    </div> --}}
            <!-- end main content-->

<x-slot:start>
    @include('partials.start-back-to-top')
</x-slot>

<x-slot:theme>
    @include('partials.theme')
</x-slot>

<x-slot:js>
     @include('partials.js')
</x-slot>
</x-app-layout>


