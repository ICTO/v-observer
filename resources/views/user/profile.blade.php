@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <div class="card-content">
                        <div class="row">
                            <div class="col s8">
                                <div class="card-title">{{ $user->name }}</div>
                                @if( $user->email )
                                    <p><strong>Email: </strong> {{ $user->email }}</p>
                                @endif
                                @if( $user->cas_username )
                                    <p><strong>CAS username: </strong> {{ $user->cas_username }}</p>
                                @endif
                                <p><strong>Created: </strong><span class="moment-date" data-datetime="{{ $user->created_at }}"></span></p>
                                <p><strong>Last Update: </strong><span class="moment-date" data-datetime="{{ $user->updated_at }}"></span></p>
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
        </div>
    </div>
</div>
@endsection
