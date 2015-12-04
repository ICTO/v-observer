@extends('layouts.blockActions')

@section('block-preview-'.$block->id)

<h5>{{ $block->data['title'] }}</h5>
<i class="material-icons left">subdirectory_arrow_right</i>
<div class="tab">
  @foreach($block->children()->get() as $child)
    @include($block_types[$child->type]::getPreviewViewName(), ['block' => $child, 'questionaire' => $questionaire])
  @endforeach

  @if(!$block->children()->get()->count())
  <div class="list-row-wrapper">
    Nothing is added under this subtitle.
  </div>
  @endif
</div>

@endsection
