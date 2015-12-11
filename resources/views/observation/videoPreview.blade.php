@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        @include('observation.videos.'.$video->type.'.create', ['video' => $video, 'video_types' => $video_types])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
