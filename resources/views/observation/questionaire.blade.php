@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">{{ $questionaire->name }}</div>
                        <p><strong>Owner: </strong><span>{{ $questionaire->owner()->get()->first()->name }}</span></p>
                        <p><strong>Created: </strong><span class="moment-date" data-datetime="{{ $questionaire->created_at }}"></span> by {{$questionaire->creator()->get()->first()->name }}</p>
                    </div>
                    <div class="card-action">
                        @can('questionaire-edit', $questionaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionaireController@getEditQuestionaire', $questionaire->id) }}"><i class="material-icons left">mode_edit</i>Edit</a>
                        @endcan
                        @can('questionaire-block-view', $questionaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionaireController@getBlocks', $questionaire->id) }}"><i class="material-icons left">assignment</i>Questions</a>
                        @endcan
                        @can('questionaire-remove', $questionaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionaireController@getRemoveQuestionaire', $questionaire->id) }}"><i class="material-icons left">delete</i>Remove</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Videos</div>
                        @if($questionaire->videos()->count())
                            @foreach( $questionaire->videos()->orderBy('created_at', 'desc')->get() as $key => $video )
                            <div class="list-row-wrapper">
                                <div class="list-row-image circle teal white-text"><i class="material-icons">videocam</i></div>
                                <a class="list-row-link has-action-button has-image waves-effect waves-light" href="{{ action('Observation\VideoController@getVideo', $video->id) }}">
                                    {{ $video->name }}
                                    <span class="teal-text text-lighten-2">
                                    @if($video->size)
                                        (<span class="numeral" data-number="{{ $video->size }}" data-format="0.0b"></span>)
                                    @endif
                                    Created <span class="moment-date" data-datetime="{{ $video->created_at }}"></span>
                                    </span>
                                </a>
                                @can('video-menu', $questionaire)
                                <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-video-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                <ul id='dropdown-video-{{ $key }}' class='dropdown-content action-btn'>
                                    @can('video-edit', $questionaire)
                                    <li><a href="{{ action('Observation\VideoController@getEditVideo', $video->id ) }}">Edit</a></li>
                                    @endcan
                                    @can('video-remove', $questionaire)
                                    <li><a href="{{ action('Observation\VideoController@getRemoveVideo', $video->id ) }}">Remove</a></li>
                                    @endcan
                                </ul>
                                @endcan
                            </div>
                            @endforeach
                        @else
                            No videos added to this questionaire
                        @endif
                    </div>
                    <div class="card-action">
                        <a class='dropdown-button btn white-text' href='#' data-activates='dropdown-add-video'><i class="material-icons left">create</i>Add Video</a>
                        <ul id='dropdown-add-video' class='dropdown-content'>
                            @foreach($video_types as $key => $class)
                            <li><a class="teal-text text-lighten-1" href="{{ action('Observation\VideoController@getCreateVideo', [$questionaire->id, $key]) }}" ><i class="material-icons left">add</i>{{ $class::getHumanName() }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
