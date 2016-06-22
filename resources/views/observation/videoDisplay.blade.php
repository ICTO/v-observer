@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 l6">
                <div class="card left-align">
                    @include('observation.videos.'.$video->type.'.display', ['video' => $video, 'video_types' => $video_types, 'questionnaire' => $questionnaire])
                    @yield('video-actions')
                </div>
            </div>
            <div class="col s12 l6">
                <div class="card left-align">
                    @section('video-analyses')
                    <div class="card-content">
                        <div class="card-title">Analyses</div>
                        @if($analyses->count())
                            @foreach( $analyses as $key => $analysis )
                                <div class="list-row-wrapper">
                                    <div class="list-row-image"><img src="/images/no_avatar.png" alt="" class="circle responsive-img"></div>
                                    <a class="list-row-link has-action-button has-image waves-effect waves-light" href="{{ action('Observation\VideoController@getAnalysis', $analysis->id) }}">
                                        {{ $analysis->creator->name }}
                                        <span class="small">(Created: <span class="moment-date" data-datetime="{{ $analysis->created_at }}"></span>)</span>
                                        @if($analysis->completed)
                                            <div class="chip light-green white-text small">completed</div>
                                        @endif
                                    </a>
                                    @can('analysis-menu', $questionnaire)
                                    <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-analysis-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                    <ul id='dropdown-analysis-{{ $key }}' class='dropdown-content action-btn'>
                                        @can('video-analysis-export', $questionnaire)
                                        <li><a href="{{ action('Observation\VideoController@getAnalysisExportType', $analysis->id) }}">Export</a></li>
                                        @endcan
                                        @can('video-analysis-remove', $questionnaire)
                                        <li><a href="{{ action('Observation\VideoController@getAnalysisRemove', $analysis->id) }}">Remove</a></li>
                                        @endcan
                                    </ul>
                                    @endcan
                                </div>
                            @endforeach
                            @include('layouts.pagination', ['paginator' => $analyses])
                        @else
                            No analyses created for this video.
                        @endif
                    </div>
                    <div class="card-action">
                        @can('video-analysis-create', $questionnaire)
                        <form method="POST" action="{{ action('Observation\VideoController@postCreateAnalysis', [$questionnaire->id, $video->id]) }}">
                            {!! csrf_field() !!}
                            <button type="submit" class="waves-effect waves-light btn white-text lighten-1" href="{{ action('Observation\VideoController@postCreateAnalysis', ['questionnaire_id' => $questionnaire->id, 'video_id' => $video->id]) }}"><i class="material-icons left">create</i>Start new analysis</button>
                        </form>
                        <br/>
                        <a class="waves-effect waves-light btn white-text lighten-1" href="{{ action('Observation\VideoController@getAnalysesExportType', ['questionnaire_id' => $questionnaire->id, 'video_id' => $video->id]) }}"><i class="material-icons left">file_download</i>Export all analyses</a>
                        @endcan
                    </div>
                    @endsection
                    @yield('video-analyses')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
