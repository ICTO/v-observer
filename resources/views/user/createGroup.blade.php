@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <form method="POST" action="{{ action('User\UserController@postCreateGroup') }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <span class="card-title">
                                Create new group
                            </span>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="text" name="name" value="{{ old('name') }}">
                                    <label for="name">Name</label>
                                </div>
                            </div>
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
