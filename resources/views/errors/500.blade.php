@extends('errors.layout')

@section('content')
<div class="title">Internal server error.</div>
{{ $exception->getMessage() }}
@endsection
