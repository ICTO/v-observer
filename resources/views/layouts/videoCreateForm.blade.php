@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m10 push-m1 l8 push-l2">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\VideoController@postCreateVideo', [$video->questionnaire_id, $video->type]) }}">
                        {!! csrf_field() !!}
                          @yield('video-create-form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
