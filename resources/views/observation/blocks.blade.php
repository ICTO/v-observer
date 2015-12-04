@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\ObservationController@postBlocks', $questionaire->id) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="card-title">
                                {{ $questionaire->name }}
                            </div>
                            @if(!$blocks->count())
                                Press the "Add" button to start building your questionaire.
                            @endif
                            @foreach( $blocks as $key => $block )
                                @include($block_types[$block->type]::getPreviewViewName(), ['block' => $block, 'questionaire' => $questionaire, 'block_types' => $block_types])
                            @endforeach
                        </div>
                        @can('questionaire-block-edit', $questionaire)
                        <div class="card-action">
                            <a class='dropdown-button btn white-text' href='#' data-activates='dropdown-add-block'><i class="material-icons left">more_vert</i>Add</a>
                            <ul id='dropdown-add-block' class='dropdown-content'>
                                @foreach($block_types as $key => $class)
                                <li><a class="teal-text text-lighten-1" href="{{ action('Observation\ObservationController@getCreateBlock', [$questionaire->id, $key]) }}" ><i class="material-icons left">add</i>{{ $class::getHumanName() }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
