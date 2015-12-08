@extends('layouts.videoEditForm')

@section('video-edit-form')
<div class="card-content">
    <div class="row">
        <div class="input-field col s12">
            <i class="material-icons prefix grey-text">text_fields</i>
            <input type="text" name="name" value="{{ old('name') ? old('name') : $video->name }}">
            <label for="title">Video name</label>
        </div>
    </div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save</button>
</div>
@endsection
