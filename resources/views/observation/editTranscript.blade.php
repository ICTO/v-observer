@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\VideoController@postEditTranscript', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <span class="card-title">
                                Edit transcript of video {{$video->name}}
                            </span>
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea id="transcript" name="transcript" class="materialize-textarea">{{ old('transcript') ? old('transcript') : $video->transcript }}</textarea>
                                    <label for="transcript">Transcript</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save transcript</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
