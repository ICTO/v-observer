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
                    @can('profile-edit', $user)
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getEditProfile', $user->id) }}"><i class="material-icons left">mode_edit</i>Edit</a>
                    </div>
                    @endcan
                </div>
            </div>
            @if(count($users))
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Users in {{ $user->name }}</div>
                        @foreach( $users as $u )
                            @if($u->group)
                            <a class="user-letter-row-wrapper waves-effect waves-light" href="{{ action('User\UserController@getDashboard', $u->id) }}">
                                <div class="btn-floating btn-large cyan user-row-circle"><i class="material-icons">group</i></div>
                                <div class="user-name has-action-button">{{ $u->name }}</div>
                            </a>
                            @else
                            <a class="user-letter-row-wrapper waves-effect waves-light" href="{{ action('User\UserController@getProfile', $u->id) }}">
                                <div class="btn-floating btn-large orange user-row-circle"><i class="material-icons">person</i></div>
                                <div class="user-name has-action-button">
                                    {{ $u->name }}
                                    @if($u->pivot->admin)
                                    <span class="small">(Admin)</span>
                                    @endif
                                </div>
                            </a>
                            @endif
                            @if(count($users) != 1)
                            @can('user-remove', $user)
                            <a class="circle btn action-btn waves-effect waves-light blue" href="{{ action('User\UserController@getRemoveUser', [$user->id, $u->id] ) }}"><i class="material-icons">delete</i></a>
                            @endcan
                            @endif
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
