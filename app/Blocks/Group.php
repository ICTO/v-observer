<?php

namespace App\Blocks;

use Validator;

class Group {

  /**
   * Validate the create form
   */
  static function validatorCreateForm($request){
    return Validator::make($request->all(), [
        'title' => 'required'
    ]);
  }

  /**
   * Validate the edit form
   */
  static function validatorEditForm($request){
    return Validator::make($request->all(), [
        'title' => 'required'
    ]);
  }

  /**
   * Process the create form
   */
  static function processCreateForm($request){
    return array(
      'title' => $request->title
    );
  }

  /**
   * Process the edit form
   */
  static function processEditForm($request){
    return array(
      'title' => $request->title
    );
  }

  /**
   * Can add child blocks
   */
  static function canAddChildBlock(){
    return true;
  }

  /**
   * return the name of the create template
   */
  static function getCreateViewName(){
    return 'observation.blocks.createGroup';
  }

  /**
   * return the name of the edit template
   */
  static function getEditViewName(){
    return 'observation.blocks.editGroup';
  }

  /**
   * return the name of the preview template
   */
  static function getPreviewViewName(){
    return 'observation.blocks.previewGroup';
  }

  /**
   * Add button name
   */
  static function getHumanName(){
    return 'Subtitle';
  }
}
