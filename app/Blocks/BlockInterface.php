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
     *
     * @return array $data
     */
    static function processCreateForm($request);

    /**
    * Transform the request data from the edit form to a data array that will be stored
    *
    * @param Request $request
    *
    * @return array $data
     */
    static function processEditForm($request);

    /**
     * Process the remove form
     *
     * @param Request $request
     */
    static function processRemoveForm($request);

    /**
     * Can this block add child blocks?
     *
     * @return boolean
     */
    static function canAddChildBlock();

    /**
     * Return the name of the preview template
     */
    static function getPreviewViewName();

    /**
     * Return the name of the create template
     */
    static function getCreateViewName();

    /**
     * Return the name of the edit template
     */
    static function getEditViewName();

    /**
     * Return the name of the remove template
     */
    static function getRemoveViewName();

}
