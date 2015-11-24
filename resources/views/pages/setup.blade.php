@extends('layouts.fullcenter')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Pages\PageController@postSetup') }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="card-title">Setup - Create Super Admin</div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="name" value="{{ old('name') }}">
                                    <label for="name">Name</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="email" name="email" value="{{ old('email') }}">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            @if( Config::get('cas.cas_hostname') )
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="cas_username" value="{{ old('cas_username') }}">
                                    <label for="cas_username">CAS Username</label>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="password" name="password" id="password">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Finish setup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
