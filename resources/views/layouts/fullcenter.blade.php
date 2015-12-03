<?php $color = "grey" ?>

<!DOCTYPE html>
<html style="height:100%; min-height:610px">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <?php $title = 'Video observation application'; ?>
    <title>{{ $title }} | V-observer</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="/stylesheets/main.css">
  </head>
  <body class="valign-wrapper" style="height:100%;">
    @include('common.messages')
    <div class="valign center-align" style="width:100%">
    @yield('content')
    </div>
    <script type="text/javascript" src="/javascript/main.js"></script>
  </body>
</html>
