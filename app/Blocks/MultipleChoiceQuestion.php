<?php

namespace App\Blocks;

use Validator;
use App\Blocks\BlockInterface;

class MultipleChoiceQuestion implements BlockInterface {

  /**
   * {@inheritdoc}
   */
  static function validatorCreateForm($request){
    return Validator::make($request->all(), [
        'question' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function validatorEditForm($request){
    return self::validatorCreateForm($request);
  }

  /**
   * {@inheritdoc}
   */
  static function processCreateForm($request){

    $data = array(
      'question' => $request->question
    );

    foreach($request->option as $option){

      if($option['text'] != "")
      $data['options'][] = array(
        'text' => $option['text'],
        'score' => ( is_numeric($option['score']) ? $option['score'] : false )
      );
    }

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  static function processRemoveForm($request){
    // no extra actions needed
  }

  /**
   * {@inheritdoc}
   */
  static function processEditForm($request){
    return self::processCreateForm($request);
  }

  /**
   * {@inheritdoc}
   */
  static function canAddChildBlock(){
    return false;
  }

  /**
   * {@inheritdoc}
   */
  static function getCreateViewName(){
    return 'observation.blocks.MultipleChoiceQuestion.create';
  }

  /**
   * {@inheritdoc}
   */
  static function getEditViewName(){
    return 'observation.blocks.MultipleChoiceQuestion.edit';
  }

  /**
   * {@inheritdoc}
   */
  static function getRemoveViewName(){
    return 'observation.blocks.MultipleChoiceQuestion.remove';
  }

  /**
   * {@inheritdoc}
   */
  static function getPreviewViewName(){
    return 'observation.blocks.MultipleChoiceQuestion.preview';
  }

  /**
   * {@inheritdoc}
   */
  static function getHumanName(){
    return 'Multiple choice question';
  }

}
