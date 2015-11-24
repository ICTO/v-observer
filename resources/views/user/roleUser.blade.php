@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    @if($admin_count_error)
                    <div class="card-content">
                        <div class="card-title">
                            Error
                        </div>
                        You can't change the role for {{ $user->name }}. You need at least 1 admin role in this group.
                    </div>
                    @else
                    <form method="POST" action="{{ action('User\UserController@postRoleUser', [$group->id, $user->id, $role] ) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="card-title">
                                Are you sure you want change the role of {{ $user->name }} to {{ $role }}?
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Change role</button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
