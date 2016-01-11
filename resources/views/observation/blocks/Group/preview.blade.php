@extends('layouts.blockActions')

@section('block-preview-'.$block->id)

<h5>{{ $block->data['title'] }}</h5>
<i class="material-icons left grey-text text-lighten-2">subdirectory_arrow_right</i>
<div class="tab">
  <div>
    <div class="blocks-container">
      @foreach($block->children()->orderBy('order', 'asc')->orderBy('id', 'asc')->get() as $child)
        @include('observation.blocks.'.$child->type.'.preview', ['block' => $child, 'questionnaire' => $questionnaire])
      @endforeach
    </div>
  </div>

  @if(!$block->children()->get()->count())
  <div class="list-row-wrapper">
    Nothing is added under this subtitle.
  </div>
  @endif
</div>

@endsection
