@extends('layouts.videoCreateForm')

@section('video-create-form')
<div class="card-content">
    <div class="card-title">Create a new Mediamosa video</div>
    <div class="row">
        <div class="input-field col s12">
            <i class="material-icons prefix grey-text">text_fields</i>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
            <label for="name">Video name</label>
        </div>
    </div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Add video</button>
</div>
@endsection
