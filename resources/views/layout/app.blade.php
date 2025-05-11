@include('layout.header')

<div class="container-xxl flex-grow-1 container-p-y">
    @yield('content')
</div>

@stack('script')

@include('layout.footer')
