@extends('layouts.blockEditForm')

@section('block-edit-form')
<div class="card-content">
    <div class="card-title">
        Edit multiple choice question
    </div>
    <div class="row questionnaire-input-count">
        <div class="input-field col s12">
            <i class="material-icons prefix grey-text">short_text</i>
            <input type="text" name="question" value="{{ old('question') ? old('question') : $block->data['question'] }}">
            <label for="name">Question</label>
        </div>
        <div class="options-wrapper">
            @for($i = 0; $i < (count(old('option')) > (isset($block->data['options']) ? count($block->data['options']) : 2 ) ? count(old('option')) : (isset($block->data['options']) ? count($block->data['options']) : 2 )); $i++)
            <div class="option-wrapper">
                <div class="input-field col s8">
                    <i class="material-icons prefix grey-text">check_box</i>
                    <input type="text" name="option[{{ $i }}][text]" value="{{ old('option.'.$i.'.text') ? old('option.'.$i.'.text') : ( isset($block->data['options'][$i]['text']) ? $block->data['options'][$i]['text'] : "") }}">
                    <label for="option[{{ $i }}][text]">Response option</label>
                </div>
                <div class="input-field col s4">
                    <i class="material-icons prefix grey-text">equalizer</i>
                    <input type="text" name="option[{{ $i }}][score]" value="{{ is_numeric(old('option.'.$i.'.score')) ? old('option.'.$i.'.score') : ( isset($block->data['options'][$i]['score']) ? $block->data['options'][$i]['score'] : "") }}">
                    <label for="option[{{ $i }}][score]">Score</label>
                </div>
            </div>
            @endfor
        </div>
        <div class="input-field col s12">
            <div class="waves-effect waves-light btn add-option-action teal darken-3"><i class="material-icons left">add</i>Add option</div>
        </div>
    </div>
</div>
<div class="card-action">
    <button class="waves-effect waves-light btn" type="submit"><i class="material-icons left">done</i>Save</button>
</div>

<!--  Template for Multiple choice question extra option -->

<template id="template-question-option" style="display:none">
    <div class="option-wrapper">
        <div class="input-field col s8">
            <i class="material-icons prefix grey-text">check_box</i>
            <input type="text" name="option[__key__][text]" value="">
            <label for="option[__key__][text]">Response option</label>
        </div>
        <div class="input-field col s4">
            <i class="material-icons prefix grey-text">equalizer</i>
            <input type="number" name="option[__key__][score]" value="">
            <label for="option[__key__][score]">Score</label>
        </div>
    </div>
</template>

@endsection

@section('javascript')
<script type="text/javascript" src="/javascript/MultipleChoiceQuestion.js"></script>
@endsection
