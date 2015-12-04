@extends('layouts.blockActions')

@section('block-preview-'.$block->id)

<div>
  <span>{{ $block->data['question'] }}</span> <span class="chip">Multiple choice question</span>
</div>
<div>
  <ul class="bullets">
  @if(isset($block->data['options']))
  @foreach( $block->data['options'] as $option )
    <li>
      <span>{{ $option['text'] }}</span>
      @if(is_numeric($option['score']))
      <span class="teal-text text-lighten-2">(Score: {{ $option['score'] }})</span>
      @else
      <span class="teal-text text-lighten-2">(No score)</span>
      @endif
    </li>
  @endforeach
  @endif
  </ul>
</div>

@endsection
