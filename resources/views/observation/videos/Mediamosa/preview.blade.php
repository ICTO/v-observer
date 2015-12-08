<div class="card-title">{{ $video->name }}
@if($video->size)
    (<span class="numeral" data-number="{{ $video->size }}" data-format="0.0b"></span>)
@endif
</div>
