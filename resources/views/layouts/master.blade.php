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
    <!-- sidebar -->
    <ul id="nav-mobile" class="side-nav fixed">
      <li class="grey lighten-4">
        <div class="section center-align">
            <img src="/images/no_avatar.png" alt="" class="circle row" width="100px">
            <a href="{{ action("User\UserController@getProfile") }}" class="normal-link row">
              <span>{{ Auth::user()->name }}</span>
            </a>
          </div>
        </div>
      </li>
      <li class="divider"></li>
      <li><a href="{{ action("User\UserController@getDashboard") }}"><i class="material-icons icon-small icon-sidebar">dashboard</i>Dashboard</a></li>
      <li><a href="{{ action("User\UserController@getGroups") }}"><i class="material-icons icon-small icon-sidebar">group_work</i>Groups</a></li>
      @yield('nav')
      <li class="divider"></li>
      <li><a href="{{ action("Auth\AuthController@getLogout") }}"><i class="material-icons icon-small icon-sidebar">lock</i>Logout</a></li>
    </ul>

    <!-- header -->
    <header>
      <nav class="teal lighten-1" role="navigate">
        <div class="nav-wrapper">
          <div class="container">
            <a id="logo-container" href="{{ action("User\UserController@getDashboard") }}" class="brand-logo">
              <span class="logo-text-navbar">V-observer</span>
            </a>
            <a href="#" data-activates="nav-mobile" class="button-collapse"><i class="material-icons">menu</i></a>
          </div>
        </div>
      </nav>
    </header>

    <!-- main -->
    <main>
      @include('common.messages')
      @yield('content')
    </main>
    <script type="text/javascript" src="/javascript/main.js"></script>
    @yield('javascript')
  </body>
</html>
