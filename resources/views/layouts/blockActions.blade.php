<div class="list-row-wrapper">
    <div class="list-row-link has-action-button">
        @yield('block-preview-'.$block->id)
    </div>
    @can('questionaire-block-edit', $questionaire)
    <a class='dropdown-button btn blue action-btn' data-alignment="right" href='#' data-activates='dropdown-block-{{ $block->id }}'><i class="material-icons">more_horiz</i></a>
    <ul id='dropdown-block-{{ $block->id }}' class='dropdown-content action-btn'>
        <li><a href="{{ action('Observation\ObservationController@getEditBlock', $block->id ) }}">Edit</a></li>
        <li><a href="{{ action('Observation\ObservationController@getRemoveBlock', $questionaire->id ) }}">Remove</a></li>
        @if( $block_types[$block->type]::canAddChildBlock() )
        <li class="divider"></li>
        <li><a href="#" class="black-text">Add child</a></li>
        @foreach($block_types as $type => $class)
        <li><a href="{{ action('Observation\ObservationController@getCreateBlock', [$questionaire->id, $type, $block->id]) }}" ><i class="material-icons left">add</i>{{ $class::getHumanName() }}</a></li>
        @endforeach
        @endif
    </ul>
    @endcan
</div>
