@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s8">
                                <div class="card-title">{{ $user->name }}</div>
                                @if( $user->email )
                                    <p><strong>Email: </strong> {{ $user->email }}</p>
                                @endif
                                <p><strong>Created: </strong><span class="moment-date" data-datetime="{{ $user->created_at }}"></span></p>
                            </div>
                            <div class="col s4">
                                <img src="/images/no_avatar.png" alt="" class="circle responsive-img right">
                            </div>
                        </div>
                    </div>
                    <!-- Add remove user button -->
                    @can('profile-view', $user)
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getProfile', $user->id) }}"><i class="material-icons left">account_circle</i>View Profile</a>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Questionnaires</div>
                        @if(count($questionnaires))
                        @foreach( $questionnaires as $key => $questionnaire )
                            <div class="list-row-wrapper">
                                <div class="list-row-image circle teal white-text"><i class="material-icons">assignment</i></div>
                                <a class="list-row-link has-action-button has-image waves-effect waves-light" href="{{ action('Observation\QuestionnaireController@getQuestionnaire', $questionnaire->id) }}">
                                    {{ $questionnaire->name }}
                                </a>
                                @can('questionnaire-menu', $questionnaire)
                                <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-questionnaire-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                <ul id='dropdown-questionnaire-{{ $key }}' class='dropdown-content action-btn'>
                                    <!--  @TODO add all buttons -->
                                    @can('questionnaire-edit', $questionnaire)
                                    <li><a href="{{ action('Observation\QuestionnaireController@getEditQuestionnaire', $questionnaire->id ) }}">Edit questionnaire</a></li>
                                    @endcan
                                    @can('questionnaire-block-view', $questionnaire)
                                    <li><a href="{{ action('Observation\QuestionnaireController@getBlocks', $questionnaire->id ) }}">Questions</a></li>
                                    @endcan
                                    @can('questionnaire-interval-edit', $questionnaire)
                                    <li><a href="{{ action('Observation\QuestionnaireController@getEditInterval', $questionnaire->id) }}">Interval</a></li>
                                    @endcan
                                    @can('questionnaire-export', $questionnaire)
                                    <li><a href="{{ action('Observation\QuestionnaireController@getExportQuestionnaire', $questionnaire->id) }}">Export</a></li>
                                    @endcan
                                    @can('questionnaire-remove', $questionnaire)
                                    <li><a href="{{ action('Observation\QuestionnaireController@getRemoveQuestionnaire', $questionnaire->id ) }}">Remove questionnaire</a></li>
                                    @endcan
                                </ul>
                                @endcan
                            </div>
                        @endforeach
                        @include('layouts.pagination', ['paginator' => $questionnaires])
                        @else
                        This user doesn't own any questionnaires.
                        @endif
                    </div>
                    @can('questionnaire-create', $user)
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getCreateQuestionnaire', $user->id) }}"><i class="material-icons left">create</i>Create</a>
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\QuestionnaireController@getImportQuestionnaire', $user->id) }}"><i class="material-icons left">create</i>Import</a>
                    </div>
                    @endcan
                </div>
            </div>
            @if(count($users))
            <div class="col s12 l6">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Users in {{ $user->name }}</div>
                        @foreach( $users as $key => $u )
                            <div class="list-row-wrapper">
                                <div class="list-row-image"><img src="/images/no_avatar.png" alt="" class="circle responsive-img"></div>
                                <a class="list-row-link has-action-button has-image waves-effect waves-light" href="{{ action('User\UserController@getProfile', $u->id) }}">
                                    {{ $u->name }}
                                    @if($u->pivot->role)
                                    <span class="small">({{ $u->pivot->role }})</span>
                                    @endif
                                </a>
                                @if(count($users) != 1)
                                @can('user-menu', $user)
                                <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-user-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                <ul id='dropdown-user-{{ $key }}' class='dropdown-content action-btn'>
                                    @can('user-role-edit', $user)
                                    @if($u->pivot->role == 'admin')
                                    <li><a href="{{ action('User\UserController@getRoleUser', [$user->id, $u->id, 'member'] ) }}">Remove admin role</a></li>
                                    @else
                                    <li><a href="{{ action('User\UserController@getRoleUser', [$user->id, $u->id, 'admin'] ) }}">Add admin role</a></li>
                                    @endif
                                    @endcan
                                    @can('user-remove', $user)
                                    <li><a href="{{ action('User\UserController@getRemoveUser', [$user->id, $u->id] ) }}">Remove user from group</a></li>
                                    @endcan
                                </ul>
                                @endcan
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @can('user-add', $user)
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getAddUser', $user->id) }}"><i class="material-icons left">person_add</i>Add user</a>
                    </div>
                    @endcan
                </div>
            </div>
            @endif
            @if($dataUsage)
            <div class="col s12 m6">
                <div class="card left-align">
                    <div class="card-content center-align">
                        <div class="card-title">Data usage</div>
                        <i class="material-icons center-align large teal-text text-lighten-1">sd_card</i>
                        <div class="numeral center-align" data-number="{{ $dataUsage }}" data-format="0.0b"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
