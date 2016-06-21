<!-- sidebar -->
<ul id="nav-mobile" class="side-nav {{ $fixed ? "fixed" : ""}}">
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
<header class="{{ $fixed ? "" : "no-padding"}}">
  <nav class="teal lighten-1" role="navigate">
    <div class="nav-wrapper">
      <div class="nav-spacer">
        <a href="#" data-activates="nav-mobile" class="button-collapse {{ $fixed ? "" : "show-on-large"}}"><i class="material-icons">menu</i></a>
        <a id="logo-container" href="{{ action("User\UserController@getDashboard") }}" class="brand-logo">
          <span class="logo-text-navbar">V-observer</span>
        </a>
        @yield('header-buttons')
      </div>
    </div>
  </nav>
</header>
