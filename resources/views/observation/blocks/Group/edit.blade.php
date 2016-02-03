@extends('layouts.blockEditForm')

@section('block-edit-form')
<div class="card-content">
    <div class="row">
        <div class="input-field col s12">
            <i class="material-icons prefix grey-text">text_fields</i>
            <input type="text" id="title" name="title" value="{{ old('title') ? old('title') : $block->data['title'] }}">
            <label for="title">Subtitle</label>
        </div>
    </div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save subtitle</button>
</div>
@endsection
