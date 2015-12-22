<h5>{{ $block->data['title'] }}</h5>
<div class="tab section">
    @foreach($block->children()->orderBy('order', 'asc')->get() as $child)
        @include('observation.blocks.'.$child->type.'.display', ['video' => $video, 'block' => $child, 'questionnaire' => $questionnaire, 'part' => $part, 'analyse' => $analysis])
    @endforeach
</div>
