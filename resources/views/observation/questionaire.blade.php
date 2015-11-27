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
                        <p><strong>Created: </strong><span class="moment-date" data-datetime="{{ $questionaire->created_at }}"></span></p>
                    </div>
                    @can('questionaire-menu', $questionaire)
                    <div class="card-action">
                        @can('questionaire-edit', $questionaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\ObservationController@getEditQuestionaire', $questionaire->id) }}"><i class="material-icons left">mode_edit</i>Edit</a>
                        @endcan
                        @can('questionaire-remove', $questionaire)
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\ObservationController@getRemoveQuestionaire', $questionaire->id) }}"><i class="material-icons left">delete</i>Remove</a>
                        @endcan
                    </div>
                    @endcan
                </div>
            </div>
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Videos</div>
                        No videos added to this questionaire
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="#"><i class="material-icons left">create</i>Add Video</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
