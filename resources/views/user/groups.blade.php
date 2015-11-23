@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <div class="card-content">
                        @if(count($groups))
                        <div class="card-title">Select a group</div>
                        @else
                        <div class="card-title">No groups found</div>
                        @endif
                        @foreach( $groups as $group )
                        <a class="user-letter-row-wrapper waves-effect waves-light" href="{{ action('User\UserController@getDashboard', $group->id) }}">
                            <div class="btn-floating btn-large cyan user-row-circle"><i class="material-icons">group</i></div>
                            <div class="user-name">{{ $group->name }}</div>
                        </a>
                        @endforeach
                    </div>
                    <div class="card-action">
                        <a class="waves-effect waves-light btn white-text" href="{{ action('User\UserController@getCreateGroup') }}"><i class="material-icons left">group_add</i>New group</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
