@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\ObservationController@postEditBlock', $block->id) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="row">
                                <div class="input-field col s12">
                                    <i class="material-icons prefix grey-text">text_fields</i>
                                    <input type="text" name="title" value="{{ old('title') ? old('title') : $block->data['title'] }}">
                                    <label for="title">Subtitle</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-action">
                            <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save subtitle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
