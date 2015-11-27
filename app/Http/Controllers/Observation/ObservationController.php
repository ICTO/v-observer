<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionaire;
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

    return Redirect::action('User\UserController@getDashboard', $owner->id)->with('status', 'Questionaire created');
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
}
