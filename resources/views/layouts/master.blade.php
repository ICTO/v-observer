<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <?php $title = 'Video observation application'; ?>
    <title>{{ $title }} | V-observer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="/stylesheets/main.css">
  </head>
  <body>
    <div class="navbar-fixed">
      <nav class="teal lighten-2" role="navigate">
        <div class="nav-wrapper container">
          <a id="logo-container" href="{{ action("User\DashboardController@getDashboard") }}" class="brand-logo">
            <span class="logo-text-navbar">V-observer</span>
          </a>
          <ul class="right hide-on-med-and-down">
            @section('nav')
            @show
            <li><a class="dropdown-button" href="#!" data-activates="dropdown1">{{ Auth::user()->name }}<i class="material-icons right">arrow_drop_down</i></a></li>
            <ul id="dropdown1" class="dropdown-content">
              <li><a href="{{ action("User\DashboardController@getDashboard") }}">Dashboard</a></li>
              <li><a href="{{ action("User\ProfileController@getProfile") }}">Profile</a></li>
              <li class="divider"></li>
              <li><a href="{{ action("Auth\AuthController@getLogout") }}">Logout</a></li>
            </ul>
          </ul>
          <ul id="nav-mobile" class="side-nav">
            <li><a href="{{ action("User\ProfileController@getProfile") }}">Profile</a></li>
            <li><a href="{{ action("User\DashboardController@getDashboard") }}">Dashboard</a></li>
            @yield('nav')
            <li class="divider"></li>
            <li><a href="{{ action("Auth\AuthController@getLogout") }}">Logout</a></li>
          </ul>
          <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
        </div>
      </nav>
    </div>
    @include('common.messages')
    @yield('content')
    <script type="text/javascript" src="/javascript/main.js"></script>
  </body>
</html>
