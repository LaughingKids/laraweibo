<!DOCTYPE html>
  <html lang="en">
  <head>
    @include('shared._head')
  </head>
  <body>
    @include('shared._header')
    <div class="container">
      <div class="col-md-offset-1 col-md-10">
        @yield('content')
        @include('shared._footer')
      </div>
    </div>
  </body>
</html>
