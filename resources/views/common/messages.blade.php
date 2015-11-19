<!-- messages -->
@if (Session::has('status'))
<div class="message" data-type="success" data-message="{{ Session::get('status') }}"></div>
@endif

<!-- errors -->
@if (count($errors) > 0)
    @foreach ($errors->all() as $error)
      <div class="message" data-type="error" data-message="{{ $error }}"></div>
    @endforeach
@endif
