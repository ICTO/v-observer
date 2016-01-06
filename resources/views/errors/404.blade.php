@extends('errors.layout')

@section('content')
<div class="title">Page not found.</div>
{{ $exception->getMessage() }}
@endsection
