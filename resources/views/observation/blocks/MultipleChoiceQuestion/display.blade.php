
<div class="question row {{ isset($analysis[$part][$block->id]) ? 'has-answer' : '' }}" id="question-{{ $part }}-{{ $block->id }}">
    <h6>
        {{ $block->data['question'] }}
        <i class="done-icon material-icons green-text" >done</i>
    </h6>
    <form method="POST" action="{{ action('Observation\VideoController@postAnalysisBlock', ['questionnaire_id' => $questionnaire->id, 'id' => $video->id]) }}">
        {!! csrf_field() !!}
        <input type="hidden" name="block_id" value="{{ $block->id }}">
        <input type="hidden" name="part" value="{{ $part }}">
        @if(isset($block->data['options']))
        @foreach( $block->data['options'] as $answer => $option )
          <input name="answer" type="radio" id="option-{{ $part }}-{{ $block->id }}-{{ $answer }}" value="{{$answer}}" {{ isset($analysis[$part][$block->id]) && $analysis[$part][$block->id] == $answer ? 'checked' : '' }} />
          <label for="option-{{ $part }}-{{ $block->id }}-{{ $answer }}">{{ $option['text'] }}
            @if(is_numeric($option['score']))
              <span class="teal-text text-lighten-2">({{ $option['score'] }})</span>
            @else
              <span class="teal-text text-lighten-2">(No score)</span>
            @endif
          </label>
        @endforeach
        @endif
    </form>
</div>
