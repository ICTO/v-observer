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
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getEditProfile', $user->id) }}"><i class="material-icons left">mode_edit</i>Edit</a>
                    </div>
                </div>
            </div>
            @if(count($users))
            <div class="col s12">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="card-title">Users in {{ $user->name }}</div>
                        @foreach( $users as $u )
                        <a class="user-letter-row-wrapper waves-effect waves-light" href="{{ action('User\UserController@getProfile', $u->id) }}">
                            <div class="btn-floating btn-large orange user-row-circle"><i class="material-icons">person</i></div>
                            <div class="user-name">{{ $u->name }}</div>
                        </a>
                        @endforeach
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getAddUser', $user->id) }}"><i class="material-icons left">person_add</i>Add user</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
