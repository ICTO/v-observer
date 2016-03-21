<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    @section('title')
        Video observation application | V-observer
    @endsection
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="/stylesheets/main.css">
  </head>
  <body>
    @include('layouts.sidenav', ["fixed" => true])

    <!-- main -->
    <main>
      @include('common.messages')
      @yield('content')
    </main>
    <script type="text/javascript" src="/javascript/main.js"></script>
    @yield('javascript')
    @if(Config::get('app.tracking_snippet'))
      <!--  Tracking -->
      {!! Config::get('app.tracking_snippet') !!}
    @endif
  </body>
</html>
