<?php

namespace App\Blocks;

use Validator;

class MultipleChoiceQuestion {

  /**
   * Validate the create form
   */
  static function validatorCreateForm($request){
    return Validator::make($request->all(), [
        'question' => 'required',
        'option.0.text' => 'required',
        'option.0.score' => 'required|numeric'
    ]);
  }

  /**
   * Process the create form
   */
  static function processCreateForm($request){

    $data = array(
      'question' => $request->question
    );

    foreach($request->option as $option){
      if(!empty($option['text']) && !empty($option['score']))
      $data['options'][] = array(
        'text' => $option['text'],
        'score' => $option['score']
      );
    }

    return $data;
  }

  /**
   * Can add child blocks
   */
  static function canAddChildBlock(){
    return false;
  }

  /**
   * return the name of the create template
   */
  static function getCreateViewName(){
    return 'observation.blocks.createMultipleChoiceQuestion';
  }

  /**
   * return the name of the edit template
   */
  static function getEditViewName(){
    return 'observation.blocks.editMultipleChoiceQuestion';
  }

  /**
   * return the name of the preview template
   */
  static function getPreviewViewName(){
    return 'observation.blocks.previewMultipleChoiceQuestion';
  }

  /**
   * Add button name
   */
  static function getHumanName(){
    return 'Multiple choice question';
  }

}
