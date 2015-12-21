<?php

namespace App\Blocks;

Interface BlockInterface {

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
     * @param Block $block
     *
     * @return array $data
     */
    static function processCreateForm($request, $block);

    /**
    * Transform the request data from the edit form to a data array that will be stored
    *
    * @param Request $request
    * @param Block $block
    *
    * @return array $data
     */
    static function processEditForm($request, $block);

    /**
     * Process the remove form
     *
     * @param Request $request
     * @param Block $block
     */
    static function processRemoveForm($request, $block);

    /**
     * Can this block add child blocks?
     *
     * @return boolean
     */
    static function canAddChildBlock();

    /**
     * get the score for an answer?
     *
     * @param string $answer
     * @param Block $block
     *
     * @return int
     */
    static function getScore($answer, $block);

    /**
     * get the anser text for an answer?
     *
     * @param string $answer
     * @param Block $block
     *
     * @return string
     */
    static function getAnswerText($answer, $block);

    /**
     * get the export name?
     *
     * @param Block $block
     *
     * @return string
     */
    static function getExportName($block);

}
