@extends('layouts.videoCreateForm')

@section('video-create-form')
<div class="card-content">
    <div class="row">
        <div class="input-field col s12">
            <i class="material-icons prefix grey-text">text_fields</i>
            <input type="text" name="name" value="{{ old('name') }}">
            <label for="name">Video name</label>
        </div>
        <div class="input-field file-field col s12">
            <div class="btn">
                <span>File</span>
                <input type="file">
            </div>
            <div class="file-path-wrapper">
                <input class="file-path validate" type="text" placeholder="Upload a file">
            </div>
        </div>
    </div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Add video</button>
</div>
@endsection
