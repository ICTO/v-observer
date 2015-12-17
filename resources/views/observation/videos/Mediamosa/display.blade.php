{{-- Video display --}}
@if($video->data['status'] == 'ready')
<div class="card-image center-align">
    <video id="player" autoplay preload class="video-js vjs-default-skin vjs-big-play-centered responsive-video" controls data-setup='{ "playbackRates": [1, 1.1, 1.2, 1.3, 1.4, 1.5] }'>
    @foreach( $video_types[$video->type]::getVideoSources($video->data['asset_id']) as $source )
        <source src="{{ $source['output'] }}" type="{{ $source['content_type'] }}">
    @endforeach
    Your browser doesn't support HTML5 videos. You can get more info about supported browsers <a href="http://caniuse.com/#feat=video">here</a>.
    </video>
</div>
<div class="card-content">
    <div class="card-title">{{ $video->name }}
    @if($video->length)
        <span class="teal-text text-lighten-2">
            (<span class="numeral" data-number="{{ $video->length }}" data-format="00:00:00"></span>)
        </span>
    @endif
    </div>
    <div class="video-wrapper center-align">

    </div>
    @if($video->transcript)
    <div class="transcript wrapper">
        {!! nl2br(e($video->transcript)) !!}
    </div>
    @endif
</div>
@section('video-actions')
    {{-- only show actions when upload is finished --}}
    @if($video->data['status'] == 'ready')
    @can('video-menu-2', $questionaire)
    <div class="card-action">
        @can('video-edit-transcript', $questionaire)
        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\VideoController@getEditTranscript', $video->id) }}"><i class="material-icons left">subtitles</i>Edit transcript</a>
        @endcan
        @if(!Route::is('/video/*/analysis'))
        @can('video-analysis', $questionaire)
        <a class="waves-effect waves-light btn white-text orange lighten-1" href="{{ action('Observation\VideoController@getAnalysis', $video->id) }}"><i class="material-icons left">art_track</i>Analysis</a>
        @endcan
        @endif
    </div>
    @endcan
    @endif
@endsection
@endif

{{-- Video processing --}}
@if($video->data['status'] == 'processing')
<div class="card-content">
    <div class="card-title">Processing {{ $video->name }}</div>
    <script> var video_id = {{ $video->id }}; var start_progress = true;</script>
    <div id="loaders"></div>
    @section('javascript')
    <script type="text/javascript" src="/javascript/VideoUpload.js"></script>
    @endsection
</div>
@endif

{{-- Video upload form --}}
@if($video->data['status'] == 'uploadticket')
<div class="card-content">
    @can('video-create', $questionaire)
        <div class="card-title">Select a file to upload</div>
        <div class="row">
            <script> var video_id = {{ $video->id }}</script>
            <form class="col s12" id="upload_form" action="{{$video->data['uploadticket_data']['action']}}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="upload_ticket" value="{{$video->data['uploadticket_data']['ticket_id']}}">
                <input type="hidden" name="transcode" value="17">
                <input type="hidden" name="create_still" value="true">
                <input type="hidden" name="APC_UPLOAD_PROGRESS" value="{{$video->data['uploadticket_data']['random_id']}}">
                <div class="row">
                    <div class="input-field file-field col s12">
                        <div class="btn">
                            <span>File</span>
                            <input id="filefield" type="file" name="file">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" placeholder="Upload a file">
                        </div>
                        <div>
                            Make sure the video file has a maximum filesize of 2GB.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                        <button id="submit_button" class="waves-effect waves-light btn" type="submit"><i class="material-icons left">cloud_upload</i>Start upload</button>
                    </div>
                </div>
                <div id="loaders"></div>
            </form>
        </div>
        @section('javascript')
        <script type="text/javascript" src="/javascript/VideoUpload.js"></script>
        @endsection
    @else
        No video uploaded yet.
    @endcan
</div>
@endif
