@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12  m10 push-m1 l8 push-l2">
                <div class="card left-align">
                    @include('observation.videos.'.$video->type.'.display', ['video' => $video, 'video_types' => $video_types, 'questionnaire' => $questionnaire])
                    @yield('video-actions')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
