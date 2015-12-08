@extends('layouts.videoRemoveForm')

@section('video-remove-form')
<div class="card-content">
    <div class="card-title">Are you sure you want to remove this video?</div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">delete</i>Remove video</button>
</div>
@endsection
