<!DOCTYPE html>
<html
  lang="en"
  class="light-style"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path=""
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title>Error - Pages | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="{{ asset('img') }}/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="{{ asset('vendor')}}/fonts/boxicons.css" />
    <link rel="stylesheet" href="{{ asset('vendor')}}/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('vendor')}}/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('css') }}/demo.css" />
    <link rel="stylesheet" href="{{ asset('vendor') }}/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="{{ asset('vendor') }}/css/pages/page-misc.css" />
    <script src="{{ asset('vendor') }}/js/helpers.js"></script>
    <script src="{{ asset('js') }}/config.js"></script>
  </head>

  <body>
    <div class="container-xxl container-p-y">
      <div class="misc-wrapper">
        <h2 class="mb-2 mx-2">Page Not Found :(</h2>
        <p class="mb-4 mx-2">Oops! 😖 The requested URL was not found on this server.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to home</a>
        <div class="mt-3">
          <img
            src="{{ asset('img') }}/illustrations/page-misc-error-light.png"
            alt="page-misc-error-light"
            width="500"
            class="img-fluid"
            data-app-dark-img="illustrations/page-misc-error-dark.png"
            data-app-light-img="illustrations/page-misc-error-light.png"
          />
        </div>
      </div>
    </div>
    <script src="{{ asset('vendor') }}/libs/jquery/jquery.js"></script>
    <script src="{{ asset('vendor') }}/libs/popper/popper.js"></script>
    <script src="{{ asset('vendor') }}/js/bootstrap.js"></script>
    <script src="{{ asset('vendor') }}/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="{{ asset('vendor') }}/js/menu.js"></script>
    <script src="{{ asset('js') }}/main.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
