@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <form method="POST" action="{{ action('User\UserController@postAddUser', $group->id) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="card-title">Add user to {{ $group->name }}</div>
                            @if(count($users))
                            <div class="row">
                                <div class="input-field col s12">
                                    <select class="icons" name="user_id">
                                      <option value="" disabled selected>Select a user</option>
                                      @foreach($users as $user)
                                      <option value="{{ $user->id }}" data-icon="/images/no_avatar.png" class="left circle">{{ $user->name }} {{ $user->email ? '('.$user->email.')' : '' }}</span></option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">person_add</i>Add user</button>
                            @else
                            <div class="card-content">
                                There are no users to add. Start by creating new users.
                            </div>
                            @endif
                        </div>
                        @can('user-create', $group)
                        <div class="card-action">
                            <a class="waves-effect waves-light btn white-text" type="submit" href="{{ action('User\UserController@getCreateUser', $group->id) }}"><i class="material-icons left">person_add</i>Create new user</a>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
