@if (count($errors) > 0 || Session::has('status'))
<div id="modal-messages" class="modal">
    <div class="modal-content">
      @if (Session::has('status'))
        <h4>{{ Session::get('status') }}</h4>
      @endif
      @if (count($errors) > 0)
      <h4>Whoops! Something went wrong!</h4>
      <ul>
          @foreach ($errors->all() as $error)
              <li><blockquote>{{ $error }}</blockquote></li>
          @endforeach
      </ul>
      @endif
    </div>
    <div class="modal-footer">
      <a href="#!" class=" modal-action modal-close waves-effect waves-teal btn-flat">Close</a>
    </div>
</div>
@endif
