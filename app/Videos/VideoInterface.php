<?php

namespace App\Videos;

Interface VideoInterface {

  /**
   * Get human name of this block
   */
  static function getHumanName();

  /**
   * Validate the create form
   *
   * @param Request $request
   *
   * @return Validator
   */
  static function validatorCreateForm($request);

  /**
   * Validate the edit form
   *
   * @param Request $request
   *
   * @return Validator
   */
  static function validatorEditForm($request);

  /**
   * Transform the request data from the create form to a data array that will be stored
   *
   * @param Request $request
   * @param int $step
   * @param Video $video
   *
   * @return array $data
   */
  static function processCreateForm($request, $video);

  /**
  * Transform the request data from the edit form to a data array that will be stored
  *
  * @param Request $request
  * @param Video $video
  *
  * @return array $data
   */
  static function processEditForm($request, $video);

  /**
   * Process the remove form
   *
   * @param Request $request
   * @param Video $video
   */
  static function processRemoveForm($request, $video);

  /**
   * Upload finished
   *
   * @param Request $request
   * @param Video $video
   */
  static function uploadFinished($request, $video);

  /**
   * Upload progress
   *
   * @param Request $request
   * @param Questionnaire $questionnaire
   * @param Video $video
   */
  static function uploadProgress($request, $questionnaire, $video);

}
