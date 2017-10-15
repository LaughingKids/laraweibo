<!DOCTYPE html>
  <html lang="en">
  <head>
    @include('shared.head')
  </head>
  <body>
    @include('shared.header')
    <div class="container">
      @yield('content')
    </div>
    <!-- end of .container -->
    @include('shared.footer')
  </body>
</html>
