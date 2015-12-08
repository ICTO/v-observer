@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\QuestionaireController@postCreateBlock', [$block->questionaire_id, $block->type, $block->parent_id]) }}">
                        {!! csrf_field() !!}
                          @yield('block-create-form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
