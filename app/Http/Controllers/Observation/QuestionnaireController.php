<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionnaire;
use App\Models\Block;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use App\Http\Controllers\Observation\VideoController;

class QuestionnaireController extends Controller
{
  /**
   * Block types
   */
  static function getBlockTypes(){
    return array(
      'Group' => '\App\Blocks\Group',
      'MultipleChoiceQuestion' => '\App\Blocks\MultipleChoiceQuestion',
    );
  }

  /**
   * Get the questionnaire.
   *
   * @return View
   */
  protected function getQuestionnaire($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-view', $questionnaire);

    $data = array(
      'questionnaire' => $questionnaire,
      'video_types' => VideoController::getVideoTypes(),
      'videos' => $questionnaire->videos()->orderBy('created_at', 'desc')->paginate(15)
    );

    return view('observation.questionnaire', $data);
  }

  /**
   * Get the form to create a questionnaire.
   *
   * @return View
   */
  protected function getCreateQuestionnaire($owner_id = false)
  {
    if($owner_id){
      $owner = User::where('id',$owner_id)->firstOrFail();
    } else {
      $owner = Auth::user();
    }

    $possible_owners = Auth::user()->groups()->withPivot('role')->where('role','admin')->get();
    $possible_owners->push(Auth::user());

    $this->authorize('questionnaire-create', $owner);

    $data = array(
      'possible_owners' => $possible_owners,
      'owner' => $owner,
    );

    return view('observation.createQuestionnaire', $data);
  }

  /**
   * save the new questionnaire
   *
   * @return View
   */
  protected function postCreateQuestionnaire(Request $request)
  {
    $owner = User::where('id',$request->owner_id)->firstOrFail();

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'owner_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\QuestionnaireController@getCreateQuestionnaire', $owner->id)
            ->withInput()
            ->withErrors($validator);
    }

    $this->authorize('questionnaire-create', $owner);

    $questionnaire = new Questionnaire();
    $questionnaire->name = $request->name;
    $questionnaire->owner_id = $owner->id;
    $questionnaire->locked = false;
    $questionnaire->interval = 300; // default 5 minutes interval
    $questionnaire->creator_id = Auth::user()->id;
    $questionnaire->save();

    return Redirect::action('Observation\QuestionnaireController@getBlocks', $questionnaire->id);
  }

  /**
   * Get the form to import a questionnaire.
   *
   * @return View
   */
  protected function getImportQuestionnaire($owner_id = false)
  {
    if($owner_id){
      $owner = User::where('id',$owner_id)->firstOrFail();
    } else {
      $owner = Auth::user();
    }

    $possible_owners = Auth::user()->groups()->withPivot('role')->where('role','admin')->get();
    $possible_owners->push(Auth::user());

    $this->authorize('questionnaire-create', $owner);

    $data = array(
      'possible_owners' => $possible_owners,
      'owner' => $owner,
    );

    return view('observation.importQuestionnaire', $data);
  }

  /**
   * save the new questionnaires import
   *
   * @return View
   */
  protected function postImportQuestionnaire(Request $request)
  {
    $owner = User::where('id',$request->owner_id)->firstOrFail();

    $validator = Validator::make($request->all(), [
        'import' => 'required|mimes:txt,json',
        'owner_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\QuestionnaireController@getImportQuestionnaire', $owner->id)
            ->withInput()
            ->withErrors($validator);
    }

    $this->authorize('questionnaire-create', $owner);

    $filePath = $request->file('import')->getRealPath();
    $content = file_get_contents($filePath);
    $data = json_decode($content);

    // @TODO : validate import file before importing

    $questionnaire = new Questionnaire();
    $questionnaire->name = $request->name;
    $questionnaire->owner_id = $owner->id;
    $questionnaire->locked = false;
    $questionnaire->interval = $data->interval;
    $questionnaire->creator_id = Auth::user()->id;
    $questionnaire->save();

    $this->importBlocks($data->blocks, $questionnaire);

    return Redirect::action('Observation\QuestionnaireController@getBlocks', $questionnaire->id);
  }

  private function importBlocks($blocks, $questionnaire, $parent = NULL){
    foreach($blocks as $block){
      $newBlock = new Block();
      $newBlock->questionnaire_id = $questionnaire->id;
      $newBlock->type = $block->type;
      $newBlock->order = $block->order;
      $newBlock->parent_id = $parent;
      $newBlock->data = $block->data;
      $newBlock->save();
      if(property_exists($block, "children")){
        $this->importBlocks($block->children, $questionnaire, $newBlock->id);
      }
    }
  }

  /**
   * Get the form to edit a questionnaire.
   *
   * @return View
   */
  protected function getEditQuestionnaire($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $possible_owners = Auth::user()->groups()->withPivot('role')->where('role','admin')->get();
    $possible_owners->push(Auth::user());

    $this->authorize('questionnaire-edit', $questionnaire);

    $data = array(
      'questionnaire' => $questionnaire,
      'possible_owners' => $possible_owners,
      'owner' => $questionnaire->owner()->get()->first(),
    );

    return view('observation.editQuestionnaire', $data);
  }

  /**
   * save the edited questionnaire
   *
   * @return View
   */
  protected function postEditQuestionnaire(Request $request, $id)
  {
    $owner = User::where('id',$request->owner_id)->firstOrFail();
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-edit', $questionnaire);

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'owner_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\QuestionnaireController@getEditQuestionnaire', $questionnaire->id)
            ->withInput()
            ->withErrors($validator);
    }

    $questionnaire->name = $request->name;
    $questionnaire->owner_id = $owner->id;
    $questionnaire->save();

    return Redirect::action('Observation\QuestionnaireController@getQuestionnaire', $questionnaire->id)->with('status', 'Questionnaire saved');
  }

  /**
   * Get the form to edit an interval.
   *
   * @return View
   */
  protected function getEditInterval($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-interval-edit', $questionnaire);

    $data = array(
      'questionnaire' => $questionnaire,
    );

    return view('observation.editInterval', $data);
  }

  /**
   * save the edited interval
   *
   * @return View
   */
  protected function postEditInterval(Request $request, $id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-interval-edit', $questionnaire);

    $validator = Validator::make($request->all(), [
        'interval' => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\QuestionnaireController@getEditInterval', $questionnaire->id)
            ->withInput()
            ->withErrors($validator);
    }

    $questionnaire->interval = $request->interval;
    $questionnaire->save();

    return Redirect::action('Observation\QuestionnaireController@getQuestionnaire', $questionnaire->id)->with('status', 'Interval saved');
  }

  /**
   * Get the form to remove a questionnaire.
   *
   * @return View
   */
  protected function getExportQuestionnaire($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-export', $questionnaire);

    $data = array(
      'blocks' => array(),
      'interval' => $questionnaire->interval
    );

    $blocks = $questionnaire->blocks()->whereNull('parent_id')->orderBy('id', 'asc')->get();
    $data['blocks'] = $this->exportBlocks($blocks);

    header ("Content-Type: application/octet-stream");
    header ("Content-disposition: attachment; filename=questionnaire-".$questionnaire->id.".json");

    print json_encode($data);
    exit();
  }

  /**
   * export blocks data
   */
  private function exportBlocks($blocks){
    $data = array();
    foreach($blocks as $block){
      $new = array(
        'type' => $block->type,
        'order' => $block->order,
        'data' => $block->data,
      );
      $children = $block->children()->orderBy('id', 'asc')->get();
      if($children->count()){
        $new['children'] = $this->exportBlocks($children);
      }
      $data[] = $new;
    }
    return $data;
  }

  /**
   * Get the form to remove a questionnaire.
   *
   * @return View
   */
  protected function getRemoveQuestionnaire($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-remove', $questionnaire);

    $data = array(
      'questionnaire' => $questionnaire
    );

    return view('observation.removeQuestionnaire', $data);
  }

  /**
   * remove the questionnaire
   *
   * @return View
   */
  protected function postRemoveQuestionnaire($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();
    $owner_id = $questionnaire->owner()->get()->first()->id;

    $this->authorize('questionnaire-remove', $questionnaire);

    $questionnaire->delete();

    return Redirect::action('User\UserController@getDashboard', $owner_id)->with('status', 'Questionnaire removed');
  }

  /**
   * Get the form to edit the questions of a questionnaire.
   *
   * @return View
   */
  protected function getBlocks($id)
  {
    $questionnaire = Questionnaire::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-block-view', $questionnaire);

    $data = array(
      'questionnaire' => $questionnaire,
      'blocks' => $questionnaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->orderBy('id', 'asc')->get(),
      'block_types' => $this->getBlockTypes()
    );

    return view('observation.blocks', $data);
  }

  /**
   * Get the form to create a block.
   *
   * @return View
   */
  protected function getCreateBlock($questionnaire_id, $type, $parent_id = NULL )
  {
    $questionnaire = Questionnaire::where('id',$questionnaire_id)->firstOrFail();

    if(!array_key_exists($type, $this->getBlockTypes())){
      abort(403, 'Block type not defined');
    }

    $block = new Block();
    $block->questionnaire_id = $questionnaire_id;
    $block->type = $type;
    $block->parent_id = $parent_id;

    $this->authorize('questionnaire-block-edit', $questionnaire);

    $data = array(
      'block' => $block,
    );

    return view('observation.blocks.'.$block->type.'.create', $data);
  }

  /**
   * save the create form from a block
   *
   * @return Redirect
   */
  protected function postCreateBlock(Request $request, $questionnaire_id, $type, $parent_id = NULL )
  {
    $questionnaire = Questionnaire::where('id',$questionnaire_id)->firstOrFail();

    $this->authorize('questionnaire-block-edit', $questionnaire);

    if(!array_key_exists($type, $this->getBlockTypes())){
      abort(403, 'Block type not defined');
    }

    $block = new Block();
    $block->questionnaire_id = $questionnaire_id;
    $block->type = $type;
    $block->parent_id = $parent_id;

    $blockTypes = $this->getBlockTypes();
    $class = $blockTypes[$type];

    $validator = $class::validatorCreateForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\QuestionnaireController@getCreateBlock', array($questionnaire->id, $type, $parent_id) )
            ->withInput()
            ->withErrors($validator);
    }

    $class::processCreateForm($request, $block);

    $block->save();

    return Redirect::action('Observation\QuestionnaireController@getBlocks', $questionnaire->id)->with('status', 'Block saved');
  }

  /**
   * Get the edit form from a block
   *
   * @return View
   */
  protected function getEditBlock($id)
  {
    $block = Block::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-block-edit', $block->questionnaire()->get()->first());

    $data = array(
      'block' => $block,
    );

    $blockTypes = $this->getBlockTypes();

    return view('observation.blocks.'.$block->type.'.edit', $data);
  }

  /**
   * save the edit form from a block
   *
   * @return Redirect
   */
  protected function postEditBlock(Request $request, $id )
  {
    $block = Block::where('id',$id)->firstOrFail();

    $questionnaire = $block->questionnaire()->get()->first();

    $this->authorize('questionnaire-block-edit', $questionnaire);

    $blockTypes = $this->getBlockTypes();
    $class = $blockTypes[$block->type];

    $validator = $class::validatorEditForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\QuestionnaireController@getEditBlock', $id )
            ->withInput()
            ->withErrors($validator);
    }

    $class::processEditForm($request, $block);

    $block->save();

    return Redirect::action('Observation\QuestionnaireController@getBlocks', $questionnaire->id)->with('status', 'Block saved');
  }

  /**
   * Get the remove form from a block
   *
   * @return View
   */
  protected function getRemoveBlock($id)
  {
    $block = Block::where('id',$id)->firstOrFail();

    $this->authorize('questionnaire-block-edit', $block->questionnaire()->get()->first());

    $data = array(
      'block' => $block,
    );

    $blockTypes = $this->getBlockTypes();

    return view('observation.blocks.'.$block->type.'.remove', $data);
  }

  /**
   * remove a block
   *
   * @return Redirect
   */
  protected function postRemoveBlock(Request $request, $id )
  {
    $block = Block::where('id',$id)->firstOrFail();

    $questionnaire = $block->questionnaire()->get()->first();

    $this->authorize('questionnaire-block-edit', $questionnaire);

    $this->removeBlock($block, $request);

    return Redirect::action('Observation\QuestionnaireController@getBlocks', $questionnaire->id)->with('status', 'Removed');
  }

  /**
   * remove child blocks
   */
  private function removeBlock($block, $request){
    $blockTypes = $this->getBlockTypes();
    $class = $blockTypes[$block->type];
    $class::processRemoveForm($request, $block);

    if($blockTypes[$block->type]::canAddChildBlock()){
      foreach ($block->children()->get() as $child_block) {
        $this->removeBlock($child_block, $request);
      }
    }

    $block->delete();
  }

}
