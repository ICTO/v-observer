@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <form method="POST" action="{{ action('User\UserController@postEditProfile', $user->id) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" id="name" name="name" value="{{ old('name') ? old('name') : $user->name }}">
                                    <label for="name">Name</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="email" id="email" name="email" value="{{ old('email') ? old('email') : $user->email }}">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            @if( Config::get('cas.cas_hostname') )
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" id="cas_username" name="cas_username" value="{{ old('cas_username') ? old('cas_username') : $user->cas_username }}">
                                    <label for="cas_username">CAS username</label>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
