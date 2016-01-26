@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\VideoController@postEditVideo', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">
                        {!! csrf_field() !!}
                        @yield('video-edit-form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
