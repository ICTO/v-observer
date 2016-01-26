@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12 m8 push-m2 l6 push-l3">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\VideoController@postAnalysisExportType', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <span class="card-title">
                                Choose an export type
                            </span>
                            <div class="row">
                                <div class="input-field col s12">
                                    <select name="type">
                                      @foreach($exportTypes as $exportType => $class)
                                      <option value="{{ $exportType }}" {{ old('type') == $exportType ? 'selected' : '' }}>{{ $class::getHumanName() }}</option>
                                      @endforeach
                                    </select>
                                    <label>Type</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">file_download</i>Download</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
