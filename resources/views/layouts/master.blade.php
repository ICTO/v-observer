<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <?php $title = 'Video observation application'; ?>
    <title>{{ $title }} | V-observer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="{{ elixir("stylesheets/main.css") }}">
  </head>
  <body>
    <div class="navbar-fixed">
      <nav role="navigate">
        <div class="nav-wrapper container">
          <a id="logo-container" href="/" class="brand-logo">V-observer</a>
          <ul class="right hide-on-med-and-down">
            @section('nav')
            <li><a href="#">Test</a></li>
            @show
          </ul>
          <ul id="nav-mobile" class="side-nav">
            @yield('nav')
          </ul>
          <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
        </div>
      </nav>
    </div>
    @section('nav')
    @include('common.errors')
    @yield('content')
    <script type="text/javascript" src="{{ elixir("javascript/main.js") }}"></script>
  </body>
</html>
