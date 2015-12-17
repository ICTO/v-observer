<?php

namespace App\Blocks;

use Validator;
use App\Blocks\BlockInterface;

class Group implements BlockInterface{

  /**
   * {@inheritdoc}
   */
  static function validatorCreateForm($request){
    return Validator::make($request->all(), [
        'title' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function validatorEditForm($request){
    return Validator::make($request->all(), [
        'title' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function processCreateForm($request, $block){
    $block->data = array(
      'title' => $request->title
    );
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
    $block->data = array(
      'title' => $request->title
    );
  }

  /**
   * {@inheritdoc}
   */
  static function canAddChildBlock(){
    return true;
  }

  /**
   * {@inheritdoc}
   */
  static function getHumanName(){
    return 'Subtitle';
  }

  /**
   * {@inheritdoc}
   */
  static function getScore($answer, $block){
    return false;
  }
}
