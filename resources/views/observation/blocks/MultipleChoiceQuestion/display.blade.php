<h6>{{ $block->data['question'] }}</h6>
<form method="POST" action="{{ action('Observation\VideoController@postAnalysisBlock', $video->id, $part, $block->id) }}">
    {!! csrf_field() !!}
    @if(isset($block->data['options']))
    @foreach( $block->data['options'] as $key => $option )
      <input name="option" type="radio" id="option-{{ $part }}-{{ $block->id }}-{{ $key }}" />
      <label for="option-{{ $part }}-{{ $block->id }}-{{ $key }}">{{ $option['text'] }}
        @if(is_numeric($option['score']))
          <span class="teal-text text-lighten-2">({{ $option['score'] }})</span>
        @else
          <span class="teal-text text-lighten-2">(No score)</span>
        @endif
      </label>
    @endforeach
    @endif

</form>
