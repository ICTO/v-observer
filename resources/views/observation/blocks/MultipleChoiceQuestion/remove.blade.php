@extends('layouts.blockRemoveForm')

@section('block-remove-form')
<div class="card-content">
    <div class="card-title">Are you sure you want to remove this question?</div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">delete</i>Remove</button>
</div>
@endsection
