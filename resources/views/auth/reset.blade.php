@extends('layouts.fullcenter')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Auth\PasswordController@postReset') }}">
                        {!! csrf_field() !!}
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="card-content">
                            <span class="card-title">Reset your password</span>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="email" name="email" value="{{ old('email') }}">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="password" name="password" id="password">
                                    <label for="password">Password</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="password" name="password_confirmation" id="password_confirmation">
                                    <label for="password_confirmation">Confirm Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">repeat</i>Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
