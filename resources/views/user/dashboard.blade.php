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
                    @can('profile-edit', $user)
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getEditProfile', $user->id) }}"><i class="material-icons left">mode_edit</i>Edit</a>
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getRemoveProfile', $user->id) }}"><i class="material-icons left">delete</i>Remove</a>
                    </div>
                    @endcan
                </div>
            </div>
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Questionaires</div>
                        @if(count($questionaires))
                        @foreach( $questionaires as $key => $questionaire )
                            <div class="list-row-wrapper">
                                <div class="list-row-image circle teal white-text"><i class="material-icons">assignment</i></div>
                                <a class="list-row-link has-action-button has-image waves-effect waves-light" href="{{ action('Observation\ObservationController@getQuestionaire', $questionaire->id) }}">
                                    {{ $questionaire->name }}
                                </a>
                                @can('questionaire-edit', $questionaire)
                                <a class='dropdown-button btn blue action-btn' href='#' data-activates='dropdown-questionaire-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                <ul id='dropdown-questionaire-{{ $key }}' class='dropdown-content action-btn'>
                                    @can('questionaire-edit', $questionaire)
                                    <li><a href="{{ action('Observation\ObservationController@getEditQuestionaire', $questionaire->id ) }}">Edit questionaire</a></li>
                                    @endcan
                                    @can('questionaire-remove', $questionaire)
                                    <li><a href="{{ action('Observation\ObservationController@getRemoveQuestionaire', $questionaire->id ) }}">Remove questionaire</a></li>
                                    @endcan
                                </ul>
                                @endcan
                            </div>
                        @endforeach
                        @else
                        This user doesn't own any questionaires.
                        @endif
                    </div>
                    @can('questionaire-create', $user)
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('Observation\ObservationController@getCreateQuestionaire', $user->id) }}"><i class="material-icons left">create</i>Add Questionaire</a>
                    </div>
                    @endcan
                </div>
            </div>
            @if(count($users))
            <div class="col s12">
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
                                @can('user-admin-actions', $user)
                                <a class='dropdown-button btn blue action-btn' href='#' data-activates='dropdown-user-{{ $key }}'><i class="material-icons">more_horiz</i></a>
                                <ul id='dropdown-user-{{ $key }}' class='dropdown-content action-btn'>
                                    @can('user-role-edit', $user)
                                    @if($u->pivot->role == 'admin')
                                    <li><a href="{{ action('User\UserController@getRoleUser', [$user->id, $u->id, 'member'] ) }}">Remove admin role</a></li>
                                    @else
                                    <li><a href="{{ action('User\UserController@getRoleUser', [$user->id, $u->id, 'admin'] ) }}">Add admin role</a></li>
                                    @endif
                                    @endcan
                                    @can('user-remove', $user)
                                    <li><a href="{{ action('User\UserController@getRemoveUser', [$user->id, $u->id] ) }}">Remove user</a></li>
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
        </div>
    </div>
</div>
@endsection
