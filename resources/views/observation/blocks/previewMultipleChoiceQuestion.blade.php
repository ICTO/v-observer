@extends('layouts.blockActions')

@section('block-preview-'.$block->id)

<div>
  <span>{{ $block->data['question'] }}</span> <span class="chip">Multiple choice question</span>
</div>
<div>
  <ul class="bullets">
  @foreach( $block->data['options'] as $option )
    <li><span>{{ $option['text'] }}</span> <span class="teal-text text-lighten-2">(Score: {{ $option['score'] }})</span></li>
  @endforeach
  </ul>
</div>

@endsection
