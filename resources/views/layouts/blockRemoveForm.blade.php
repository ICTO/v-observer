@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\QuestionnaireController@postRemoveBlock', [$block->id]) }}">
                        {!! csrf_field() !!}
                        @yield('block-remove-form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
