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
  static function processCreateForm($request){
    return array(
      'title' => $request->title
    );
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
    return array(
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
  static function getCreateViewName(){
    return 'observation.blocks.Group.create';
  }

  /**
   * {@inheritdoc}
   */
  static function getEditViewName(){
    return 'observation.blocks.Group.edit';
  }

  /**
   * {@inheritdoc}
   */
  static function getRemoveViewName(){
    return 'observation.blocks.Group.remove';
  }

  /**
   * {@inheritdoc}
   */
  static function getPreviewViewName(){
    return 'observation.blocks.Group.preview';
  }

  /**
   * {@inheritdoc}
   */
  static function getHumanName(){
    return 'Subtitle';
  }
}
