@extends('layouts.master')

@section('content')
<div class="container">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <div class="card left-align">
                    <form method="POST" action="{{ action('Observation\QuestionnaireController@postOrderBlocks', $questionnaire->id) }}">
                        {!! csrf_field() !!}
                        <div class="card-content">
                            <div class="card-title">
                                <a class="black-text" href="{{ action('Observation\QuestionnaireController@getQuestionnaire', $questionnaire->id) }}">{{ $questionnaire->name }}</a>
                                @if($questionnaire->locked)
                                    <span class="teal-text text-lighten-1">(Locked because analysis started)</span>
                                @endif
                            </div>
                            @if(!$blocks->count())
                                Press the "Add" button to start building your questionnaire.
                            @endif
                            <div class="blocks-container">
                                @foreach( $blocks as $block )
                                    @include('observation.blocks.'.$block->type.'.preview', ['block' => $block, 'questionnaire' => $questionnaire, 'block_types' => $block_types])
                                @endforeach
                            </div>
                        </div>
                        @can('questionnaire-block-edit', $questionnaire)
                        <div class="card-action">
                            <a class='dropdown-button btn white-text' href='#' data-activates='dropdown-add-block'><i class="material-icons left">more_vert</i>Add</a>
                            <ul id='dropdown-add-block' class='dropdown-content'>
                                @foreach($block_types as $key => $class)
                                @if($class::canAddChildBlock())
                                <li><a class="teal-text text-lighten-1" href="{{ action('Observation\QuestionnaireController@getCreateBlock', [$questionnaire->id, $key]) }}" ><i class="material-icons left">add</i>{{ $class::getHumanName() }}</a></li>
                                @endif
                                @endforeach
                            </ul>
                            @if($blocks->count())
                            <button type="submit" class='waves-effect waves-light btn white-text' href='#'><i class="material-icons left">reorder</i>Save order</button>
                            @endif
                        </div>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
    @can('questionnaire-block-edit', $questionnaire)
    <script type="text/javascript" src="/javascript/BlocksOrder.js"></script>
    @endcan
@endsection
