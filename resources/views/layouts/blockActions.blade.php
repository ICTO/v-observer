<div class="list-row-wrapper" data-block-id="{{ $block->id }}">
    <div class="list-row-link has-action-button">
        <input type="hidden" name="blocks[{{ $block->id }}][parent_id]" value="{{ $block->parent_id }}">
        <input type="hidden" name="blocks[{{ $block->id }}][order]" value="{{ $block->order }}">
        @yield('block-preview-'.$block->id)
    </div>

    @can('questionnaire-block-edit', $questionnaire)
    <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-block-{{ $block->id }}'><i class="material-icons">more_vert</i></a>
    <ul id='dropdown-block-{{ $block->id }}' class='dropdown-content action-btn'>
        <li><a href="{{ action('Observation\QuestionnaireController@getEditBlock', $block->id ) }}">Edit</a></li>
        <li><a href="{{ action('Observation\QuestionnaireController@getRemoveBlock', $block->id ) }}">Remove</a></li>
        @if( $block_types[$block->type]::canCopyBlock() )
        <li><a href="{{ action('Observation\QuestionnaireController@getCopyBlock', $block->id ) }}">Copy</a></li>
        @endif
        @if( $block_types[$block->type]::canAddChildBlock() )
        <li class="divider"></li>
        <li><a href="#" class="black-text">Add child</a></li>
        @foreach($block_types as $type => $class)
        <li><a href="{{ action('Observation\QuestionnaireController@getCreateBlock', [$questionnaire->id, $type, $block->id]) }}" ><i class="material-icons left">add</i>{{ $class::getHumanName() }}</a></li>
        @endforeach
        @endif
    </ul>
    @endcan
</div>
