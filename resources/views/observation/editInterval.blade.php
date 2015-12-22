@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\QuestionnaireController@postEditInterval', $questionnaire->id) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <span class="card-title">
                                Edit the interval of this questionnaire
                            </span>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input type="number" id="interval" name="interval" value="{{ old('interval') ? old('interval') : $questionnaire->interval }}">
                                    <label for="interval">Interval in seconds</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save interval</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
