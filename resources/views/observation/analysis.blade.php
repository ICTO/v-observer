@extends('layouts.full')

@section('content')
<div>
    <div class="section">
        <div class="row">
            <div class="col s12 m6">
                <div class="card left-align">
                    @include('observation.videos.'.$video->type.'.display', ['video' => $video, 'video_types' => $video_types, 'questionaire' => $questionaire])
                </div>
            </div>
            <div class="col s12 m6">
                <div class="card left-align">
                    empty box
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
