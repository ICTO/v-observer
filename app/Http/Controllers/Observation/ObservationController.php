<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionaire;
use App\Models\Block;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Redirect;

class ObservationController extends Controller
{
  /**
   * Get the questionaire.
   *
   * @return View
   */
  protected function getQuestionaire($id)
  {
    $questionaire = Questionaire::where('id',$id)->firstOrFail();

    $this->authorize('questionaire-view', $questionaire);

    $data = array(
      'questionaire' => $questionaire,
    );

    return view('observation.questionaire', $data);
  }

  /**
   * Get the form to create a questionaire.
   *
   * @return View
   */
  protected function getCreateQuestionaire($owner_id = false)
  {
    if($owner_id){
      $owner = User::where('id',$owner_id)->firstOrFail();
    } else {
      $owner = Auth::user();
    }

    $possible_owners = Auth::user()->groups()->withPivot('role')->where('role','admin')->get();
    $possible_owners->push(Auth::user());

    $this->authorize('questionaire-create', $owner);

    $data = array(
      'possible_owners' => $possible_owners,
      'owner' => $owner,
    );

    return view('observation.createQuestionaire', $data);
  }

  /**
   * save the new questionaire
   *
   * @return View
   */
  protected function postCreateQuestionaire(Request $request)
  {
    $owner = User::where('id',$request->owner_id)->firstOrFail();

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'owner_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\ObservationController@getCreateQuestionaire', $owner->id)
            ->withInput()
            ->withErrors($validator);
    }

    $this->authorize('questionaire-create', $owner);

    $questionaire = new Questionaire();
    $questionaire->name = $request->name;
    $questionaire->owner_id = $owner->id;
    $questionaire->creator_id = Auth::user()->id;
    $questionaire->save();

    return Redirect::action('Observation\ObservationController@getBlocks', $questionaire->id);
  }

  /**
   * Get the form to edit a questionaire.
   *
   * @return View
   */
  protected function getEditQuestionaire($id)
  {
    $questionaire = Questionaire::where('id',$id)->firstOrFail();

    $possible_owners = Auth::user()->groups()->withPivot('role')->where('role','admin')->get();
    $possible_owners->push(Auth::user());

    $this->authorize('questionaire-edit', $questionaire);

    $data = array(
      'questionaire' => $questionaire,
      'possible_owners' => $possible_owners,
      'owner' => $questionaire->owner()->get()->first(),
    );

    return view('observation.editQuestionaire', $data);
  }

  /**
   * save the edited questionaire
   *
   * @return View
   */
  protected function postEditQuestionaire(Request $request, $id)
  {
    $owner = User::where('id',$request->owner_id)->firstOrFail();
    $questionaire = Questionaire::where('id',$id)->firstOrFail();

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'owner_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\ObservationController@getEditQuestionaire', $questionaire->id)
            ->withInput()
            ->withErrors($validator);
    }

    $this->authorize('questionaire-edit', $questionaire);

    $questionaire->name = $request->name;
    $questionaire->owner_id = $owner->id;
    $questionaire->save();

    return Redirect::action('Observation\ObservationController@getQuestionaire', $questionaire->id)->with('status', 'Questionaire saved');
  }

  /**
   * Get the form to remove a questionaire.
   *
   * @return View
   */
  protected function getRemoveQuestionaire($id)
  {
    $questionaire = Questionaire::where('id',$id)->firstOrFail();

    $this->authorize('questionaire-remove', $questionaire);

    $data = array(
      'questionaire' => $questionaire
    );

    return view('observation.removeQuestionaire', $data);
  }

  /**
   * remove the questionaire
   *
   * @return View
   */
  protected function postRemoveQuestionaire($id)
  {
    $questionaire = Questionaire::where('id',$id)->firstOrFail();
    $owner_id = $questionaire->owner()->get()->first()->id;

    $this->authorize('questionaire-remove', $questionaire);

    $questionaire->delete();

    return Redirect::action('User\UserController@getDashboard', $owner_id)->with('status', 'Questionaire removed');
  }

  /**
   * Get the form to edit the questions of a questionaire.
   *
   * @return View
   */
  protected function getBlocks($id)
  {
    $questionaire = Questionaire::where('id',$id)->firstOrFail();

    $this->authorize('questionaire-block-view', $questionaire);

    $data = array(
      'questionaire' => $questionaire,
      'blocks' => $questionaire->blocks()->whereNull('parent_id')->get(),
      'block_types' => $this->getBlockTypes()
    );

    return view('observation.blocks', $data);
  }

  /**
   * save the order of the blocks
   *
   * @return Redirect
   */
  protected function postBlocks(Request $request, $id)
  {
    $questionaire = Questionaire::where('id',$id)->firstOrFail();

    $this->authorize('questionaire-block-edit', $questionaire);

    // @TODO : save blocks order

    return Redirect::action('Observation\ObservationController@getBlocks', $questionaire->id)->with('status', 'Blocks order saved');
  }

  /**
   * Get the form to create a block.
   *
   * @return View
   */
  protected function getCreateBlock($questionaire_id, $type, $parent_id = NULL )
  {
    $questionaire = Questionaire::where('id',$questionaire_id)->firstOrFail();

    if(!array_key_exists($type, $this->getBlockTypes())){
      abort(403, 'Block type not defined');
    }

    $block = new Block();
    $block->questionaire_id = $questionaire_id;
    $block->type = $type;
    $block->parent_id = $parent_id;

    $this->authorize('questionaire-block-edit', $questionaire);

    $blockTypes = $this->getBlockTypes();

    $data = array(
      'block' => $block,
    );

    return view($blockTypes[$block->type]::getCreateViewName(), $data);
  }

  /**
   * save the create form from a block
   *
   * @return Redirect
   */
  protected function postCreateBlock(Request $request, $questionaire_id, $type, $parent_id = NULL )
  {
    $questionaire = Questionaire::where('id',$questionaire_id)->firstOrFail();

    $this->authorize('questionaire-block-edit', $questionaire);

    if(!array_key_exists($type, $this->getBlockTypes())){
      abort(403, 'Block type not defined');
    }

    $block = new Block();
    $block->questionaire_id = $questionaire_id;
    $block->type = $type;
    $block->parent_id = $parent_id;

    $blockTypes = $this->getBlockTypes();
    $class = $blockTypes[$type];

    $validator = $class::validatorCreateForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\ObservationController@getCreateBlock', array($questionaire->id, $type, $parent_id) )
            ->withInput()
            ->withErrors($validator);
    }

    $block->data = $class::processCreateForm($request);

    $block->save();

    return Redirect::action('Observation\ObservationController@getBlocks', $questionaire->id)->with('status', 'Block saved');
  }

  /**
   * Get the edit form from a block
   *
   * @return View
   */
  protected function getEditBlock($id)
  {
    $block = Block::where('id',$id)->firstOrFail();

    $this->authorize('questionaire-block-edit', $block->questionaire()->get());

    $data = array(
      'block' => $block,
    );

    $blockTypes = $this->getBlockTypes();

    return view($blockTypes[$block->type]::getEditViewName(), $data);
  }

  /**
   * save the edit form from a block
   *
   * @return Redirect
   */
  protected function postEditBlock(Request $request, $id )
  {
    $block = Block::where('id',$id)->firstOrFail();

    $questionaire = $block->questionaire()->get()->first();

    $this->authorize('questionaire-block-edit', $questionaire);

    $blockTypes = $this->getBlockTypes();
    $class = $blockTypes[$block->type];

    $validator = $class::validatorEditForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\ObservationController@getEditBlock', $id )
            ->withInput()
            ->withErrors($validator);
    }

    $block->data = $class::processEditForm($request);

    $block->save();

    return Redirect::action('Observation\ObservationController@getBlocks', $questionaire->id)->with('status', 'Block saved');
  }

  /**
   * Block types
   */
  protected function getBlockTypes(){
    return array(
      'Group' => '\App\Blocks\Group',
      'MultipleChoiceQuestion' => '\App\Blocks\MultipleChoiceQuestion',
    );
  }
}
