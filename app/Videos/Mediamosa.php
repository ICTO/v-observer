<?php

namespace App\Videos;

use App\Videos\VideoInterface;
use Validator;

class Mediamosa implements VideoInterface{

  /**
   * {@inheritdoc}
   */
  static function validatorCreateForm($request){
    return Validator::make($request->all(), [
        'name' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function validatorEditForm($request){
    return Validator::make($request->all(), [
        'name' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function processCreateForm($request){
    if( !isset($_GET['step']) || $_GET['step'] == 1 ){
      return array(
        'status' => 'noVideo'
      );
    }
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
    return array();
  }

  /**
   * {@inheritdoc}
   */
  static function getPreviewViewName(){
    return 'observation.videos.Mediamosa.preview';
  }

  /**
   * {@inheritdoc}
   */
  static function getCreateViewName(){
    return 'observation.videos.Mediamosa.create';
  }

  /**
   * {@inheritdoc}
   */
  static function getEditViewName(){
    return 'observation.videos.Mediamosa.edit';
  }

  /**
   * {@inheritdoc}
   */
  static function getRemoveViewName(){
    return 'observation.videos.Mediamosa.remove';
  }

  /**
   * {@inheritdoc}
   */
  static function getHumanName(){
    return 'Mediamosa video';
  }
}
