@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">{{ $questionnaire->name }}</div>
                        <p><strong>Owner: </strong><span>{{ $questionnaire->owner()->get()->first()->name }}</span></p>
                        <p><strong>Created: </strong><span class="moment-date" data-datetime="{{ $questionnaire->created_at }}"></span> by {{$questionnaire->creator()->get()->first()->name }}</p>
                        <p><strong>Interval: </strong><span class="numeral" data-number="{{ $questionnaire->interval }}" data-format="00:00:00"></span>
                        @if($questionnaire->locked)
                            <span class="teal-text text-lighten-1">(Interval locked because analysis started)</span>
                        @endif
                        </p>
                    </div>
                    <div class="card-action">
                        @can('questionnaire-edit', $questionnaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getEditQuestionnaire', $questionnaire->id) }}"><i class="material-icons left">mode_edit</i>Edit</a>
                        @endcan
                        @can('questionnaire-block-view', $questionnaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getBlocks', $questionnaire->id) }}"><i class="material-icons left">assignment</i>Questions</a>
                        @endcan
                        @can('questionnaire-interval-edit', $questionnaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getEditInterval', $questionnaire->id) }}"><i class="material-icons left">mode_edit</i>Interval</a>
                        @endcan
                        @can('questionnaire-export', $questionnaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getExportQuestionnaire', $questionnaire->id) }}"><i class="material-icons left">file_download</i>Export</a>
                        @endcan
                        @can('questionnaire-remove', $questionnaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getRemoveQuestionnaire', $questionnaire->id) }}"><i class="material-icons left">delete</i>Remove</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Videos</div>
                        @if($videos->count())
                            @foreach( $videos as $key => $video )
                            <div class="list-row-wrapper">
                                <div class="list-row-image circle {{ $video->analysis == 'no' ? 'cyan' : '' }}{{ $video->analysis == 'running' ? 'orange' : '' }}{{ $video->analysis == 'done' ? 'light-green' : '' }} lighten-1 white-text"><i class="material-icons">videocam</i></div>
                                <a class="list-row-link has-action-button has-image waves-effect waves-light" href="{{ action('Observation\VideoController@getVideo', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">
                                    {{ $video->name }}
                                    <span class="teal-text text-lighten-2">
                                    @if($video->length)
                                        (<span class="numeral" data-number="{{ $video->length }}" data-format="00:00:00"></span>)
                                    @endif
                                    Created <span class="moment-date" data-datetime="{{ $video->created_at }}"></span>
                                    </span>
                                </a>
                                @can('video-menu', $questionnaire)
                                <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-video-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                <ul id='dropdown-video-{{ $key }}' class='dropdown-content action-btn'>

                                    @can('video-edit', $questionnaire)
                                    <li><a href="{{ action('Observation\VideoController@getEditVideo', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id] ) }}">Edit</a></li>
                                    @endcan
                                    @can('video-edit-transcript', $questionnaire)
                                    <li><a href="{{ action('Observation\VideoController@getEditTranscript', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">Transcript</a></li>
                                    @endcan
                                    @can('video-analysis', $questionnaire)
                                    <li><a href="{{ action('Observation\VideoController@getAnalysis', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">Analysis</a></li>
                                    @endcan
                                    @if($video->analysis == 'done')
                                        @can('video-analysis-export', $questionnaire)
                                        <li><a href="{{ action('Observation\VideoController@getAnalysisExportType', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">Export</a></li>
                                        @endcan
                                    @endif
                                    @can('video-remove', $questionnaire)
                                    <li><a href="{{ action('Observation\VideoController@getRemoveVideo', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id] ) }}">Remove</a></li>
                                    @endcan
                                </ul>
                                @endcan
                            </div>
                            @endforeach
                            @include('layouts.pagination', ['paginator' => $videos])
                        @else
                            No videos added to this questionnaire
                        @endif
                    </div>
                    @can('video-create', $questionnaire)
                    <div class="card-action">
                        <a class='dropdown-button btn white-text' href='#' data-activates='dropdown-add-video'><i class="material-icons left">more_vert</i>Add Video</a>
                        <ul id='dropdown-add-video' class='dropdown-content'>
                            @foreach($video_types as $key => $class)
                            <li><a class="teal-text text-lighten-1" href="{{ action('Observation\VideoController@getCreateVideo', [$questionnaire->id, $key]) }}" ><i class="material-icons left">add</i>{{ $class::getHumanName() }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
