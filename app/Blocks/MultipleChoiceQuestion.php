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
  static function processCreateForm($request, $block){

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

    $block->data = $data;
  }

  /**
   * {@inheritdoc}
   */
  static function processRemoveForm($request, $block){
    // no extra actions needed
  }

  /**
   * {@inheritdoc}
   */
  static function processEditForm($request, $block){
    return self::processCreateForm($request, $block);
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
  static function getHumanName(){
    return 'Multiple choice question';
  }

}
