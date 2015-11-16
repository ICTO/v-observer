@if (count($errors) > 0)
<div id="modal-errors" class="modal">
    <div class="modal-content">
      <h4>Whoops! Something went wrong!</h4>
      <ul>
          @foreach ($errors->all() as $error)
              <li><blockquote>{{ $error }}</blockquote></li>
          @endforeach
      </ul>
    </div>
    <div class="modal-footer">
      <a href="#!" class=" modal-action modal-close waves-effect waves-teal btn-flat">Close</a>
    </div>
</div>
@endif
