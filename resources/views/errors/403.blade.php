@extends('errors.layout')

@section('content')
<div class="title">Forbidden.</div>
{{ $exception->getMessage() }}
@endsection
