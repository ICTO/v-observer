<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionaire;
use App\Models\Video;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Redirect;

class VideoController extends Controller
{
  /**
   * Get the video.
   *
   * @return View
   */
  protected function getVideo($id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $data = array(
      'video' => $video,
      'video_types' => $this->getVideoTypes()
    );

    return view('observation.videoPreview', $data);
  }

  /**
   * Get the form to create a video.
   *
   * @return View
   */
  protected function getCreateVideo($questionaire_id, $type)
  {
    $questionaire = Questionaire::where('id',$questionaire_id)->firstOrFail();

    if(!array_key_exists($type, $this->getVideoTypes())){
      abort(403, 'Video type not defined');
    }

    $video = new Video();
    $video->type = $type;
    $video->questionaire_id = $questionaire_id;
    $video->creator_id = Auth::user()->id;

    $this->authorize('video-create', $questionaire);

    $videoTypes = $this->getVideoTypes();

    $data = array(
      'video' => $video
    );

    return view($videoTypes[$video->type]::getCreateViewName(), $data);
  }

  /**
   * save the create form from a video
   *
   * @return Redirect
   */
  protected function postCreateVideo(Request $request, $questionaire_id, $type)
  {
    $questionaire = Questionaire::where('id',$questionaire_id)->firstOrFail();

    $this->authorize('video-create', $questionaire);

    if(!array_key_exists($type, $this->getVideoTypes())){
      abort(403, 'Video type not defined');
    }

    $video = new Video();
    $video->type = $type;
    $video->questionaire_id = $questionaire_id;
    $video->creator_id = Auth::user()->id;

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$type];

    $validator = $class::validatorCreateForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getCreateVideo', array($questionaire->id, $type) )
            ->withInput()
            ->withErrors($validator);
    }

    $video->name = $request->name;
    $video->data = $class::processCreateForm($request);

    $video->save();

    return Redirect::action('Observation\QuestionaireController@getQuestionaire', $questionaire->id)->with('status', 'Video saved');
  }

  /**
   * Get the edit form from a video
   *
   * @return View
   */
  protected function getEditVideo($id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $this->authorize('video-edit', $video->questionaire()->get()->first());

    $data = array(
      'video' => $video,
    );

    $videoTypes = $this->getVideoTypes();

    return view($videoTypes[$video->type]::getEditViewName(), $data);
  }

  /**
   * save the edit form from a video
   *
   * @return Redirect
   */
  protected function postEditVideo(Request $request, $id )
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-edit', $questionaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    $validator = $class::validatorEditForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getEditVideo', $id )
            ->withInput()
            ->withErrors($validator);
    }

    $video->name = $request->name;
    $video->data = array_merge($video->data , $class::processEditForm($request));

    $video->save();

    return Redirect::action('Observation\QuestionaireController@getQuestionaire', $video->questionaire_id)->with('status', 'Video saved');
  }

  /**
   * Get the remove form from a video
   *
   * @return View
   */
  protected function getRemoveVideo($id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-remove', $questionaire);

    $data = array(
      'video' => $video,
    );

    $videoTypes = $this->getVideoTypes();

    return view($videoTypes[$video->type]::getRemoveViewName(), $data);
  }

  /**
   * remove a video
   *
   * @return Redirect
   */
  protected function postRemoveVideo(Request $request, $id )
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-remove', $questionaire);

    $video->delete();

    return Redirect::action('Observation\QuestionaireController@getQuestionaire', $video->questionaire_id)->with('status', 'Removed video');
  }

  /**
   * Video types
   */
  static function getVideoTypes(){
    return array(
      'Mediamosa' => '\App\Videos\Mediamosa',
    );
  }
}
