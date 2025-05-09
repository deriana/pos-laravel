<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
            ©
            <script>
                document.write(new Date().getFullYear());
            </script>
            , {{ env('APP_NAME') }}
            <p class="footer-link fw-bolder">Deryana</p>
        </div>
        <div>
            <a href="https://github.com/deriana" class="footer-link me-4" target="_blank">Github</a>
            <a href="https://www.facebook.com/jerri.maruf" class="footer-link me-4" target="_blank">Facebook</a>
            <a href="https://www.instagram.com/hi_deri_/" target="_blank" class="footer-link me-4">Instagram</a>
            <a href="https://x.com/Deriana765" target="_blank" class="footer-link me-4">X</a>
        </div>
    </div>
</footer>
<!-- / Footer -->

<div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
</div>
<!-- / Layout page -->
</div>

<!-- Overlay -->
<div class="layout-overlay layout-menu-toggle"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('vendor') }}/libs/jquery/jquery.js"></script>
<script src="{{ asset('vendor') }}/libs/popper/popper.js"></script>
<script src="{{ asset('vendor') }}/js/bootstrap.js"></script>
<script src="{{ asset('vendor') }}/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

<script src="{{ asset('vendor') }}/js/menu.js"></script>
<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('vendor') }}/libs/apex-charts/apexcharts.js"></script>

<!-- Main JS -->
<script src="{{ asset('js') }}/main.js"></script>

<!-- Page JS -->
<script src="{{ asset('js') }}/dashboards-analytics.js"></script>

<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
@stack('scripts')
</body>

</html>
