<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionnaire;
use App\Models\Video;
use App\Models\Block;
use App\Models\Analysis;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Redirect;
use Response;
use App\Http\Controllers\Observation\QuestionnaireController;

class VideoController extends Controller
{
  /**
   * Video types
   */
  static function getVideoTypes(){
    return array(
      'Mediamosa' => '\App\Videos\Mediamosa',
    );
  }

  /**
   * Export types
   */
  protected function getAnalysisExportTypes(){
    return array(
      'xslx' => '\App\Export\Excel'
    );
  }

  /**
   * Get the video.
   *
   * @return View
   */
  protected function getVideo($questionnaire_id, $id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $video_types = $this->getVideoTypes();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-view', $questionnaire);

    $data = array(
      'video' => $video,
      'video_types' => $video_types,
      'questionnaire' => $questionnaire
    );

    return view('observation.videoDisplay', $data);
  }

  /**
   * Get the form to create a video.
   *
   * @return View
   */
  protected function getCreateVideo($questionnaire_id, $type)
  {
    $questionnaire = Questionnaire::where('id',$questionnaire_id)->firstOrFail();

    if(!array_key_exists($type, $this->getVideoTypes())){
      abort(403, 'Video type not defined');
    }

    $video = new Video();
    $video->type = $type;
    $video->questionnaire_id = $questionnaire_id;
    $video->creator_id = Auth::user()->id;

    $this->authorize('video-create', $questionnaire);

    $videoTypes = $this->getVideoTypes();

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire
    );

    return view('observation.videos.'.$video->type.'.create', $data);
  }

  /**
   * save the create form from a video
   *
   * @return Redirect
   */
  protected function postCreateVideo(Request $request, $questionnaire_id, $type)
  {
    $questionnaire = Questionnaire::where('id',$questionnaire_id)->firstOrFail();

    $this->authorize('video-create', $questionnaire);

    if(!array_key_exists($type, $this->getVideoTypes())){
      abort(403, 'Video type not defined');
    }

    $video = new Video();
    $video->type = $type;
    $video->creator_id = Auth::user()->id;
    $video->analysis = "no";

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$type];

    $validator = $class::validatorCreateForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getCreateVideo', array($questionnaire->id, $type) )
            ->withInput()
            ->withErrors($validator);
    }

    $video->name = $request->name;
    $class::processCreateForm($request, $video);

    $video->save();
    $video->questionnaires()->attach($questionnaire_id);

    return Redirect::action('Observation\VideoController@getVideo', array($questionnaire->id, $video->id));
  }

  /**
   * Get the edit form from a video
   *
   * @return View
   */
  protected function getEditVideo($questionnaire_id, $id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-edit', $questionnaire);

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire
    );

    $videoTypes = $this->getVideoTypes();

    return view('observation.videos.'.$video->type.'.edit', $data);
  }

  /**
   * save the edit form from a video
   *
   * @return Redirect
   */
  protected function postEditVideo(Request $request, $questionnaire_id, $id )
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-edit', $questionnaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    $validator = $class::validatorEditForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getEditVideo', array($questionnaire_id, $id) )
            ->withInput()
            ->withErrors($validator);
    }

    $video->name = $request->name;
    $class::processEditForm($request);

    $video->save();

    return Redirect::action('Observation\QuestionnaireController@getQuestionnaire', $video->questionnaire_id)->with('status', 'Video saved');
  }

  /**
   * Get the remove form from a video
   *
   * @return View
   */
  protected function getRemoveVideo($questionnaire_id, $id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-remove', $questionnaire);

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire
    );

    $videoTypes = $this->getVideoTypes();

    return view('observation.videos.'.$video->type.'.remove', $data);
  }

  /**
   * remove a video
   *
   * @return Redirect
   */
  protected function postRemoveVideo(Request $request, $questionnaire_id, $id )
  {
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-remove', $questionnaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    $class::processRemoveForm($request, $video);

    $video->delete();

    return Redirect::action('Observation\QuestionnaireController@getQuestionnaire', $questionnaire->id)->with('status', 'Removed video');
  }

  /**
   * Process when the upload is finished
   */
  protected function getUploadFinished(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-edit', $questionnaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    return response()->json($class::uploadFinished($request, $video));
  }

  /**
   * Get the upload progress
   */
  protected function getUploadProgress(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-edit', $questionnaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    return response()->json($class::uploadProgress($request, $questionnaire, $video));
  }

  /**
   * Get the edit form for the transcript of a video
   */
  protected function getEditTranscript(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-edit-transcript', $questionnaire);

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire
    );

    return view('observation.editTranscript', $data);

  }

  /**
   * Process the edit form for the transcript of a video
   */
  protected function postEditTranscript(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-edit-transcript', $questionnaire);

    $video->transcript = $request->transcript;
    $video->save();

    return Redirect::action('Observation\VideoController@getVideo', array($questionnaire->id, $video->id));
  }

  /**
   * Get the edit form for the transcript of a video
   */
  protected function getAnalysis(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    if(!$questionnaire->locked){
      $questionnaire->locked = true;
      $questionnaire->save();
    }

    if($video->analysis == "no"){
      $video->analysis = "running";
      $video->save();
    }

    $this->authorize('video-analysis', $questionnaire);

    $intervals = array();
    $position = 0;
    $end = 0;
    $chapters = array();

    while($position < $video->length){
      $start = $position;
      $end += $questionnaire->interval;
      if($end > $video->length){
        $end = $video->length;
      }
      $chapters[] = array(
        'start' => $start,
        'end' => $end,
        'percentage' => (($end - $start)/$video->length)*100, // @TODO : set percentage in javascript for more accuracy
      );
      $position = $end;
    }

    // get the analysis
    $analysis = Analysis::where('video_id', $video->id)->get();
    $analysis_ordered = array();
    foreach ($analysis as  $row) {
      $analysis_ordered[$row->part][$row->block_id] = $row->answer;
    }

    $data = array(
      'video' => $video,
      'video_types' => $this->getVideoTypes(),
      'block_types' => QuestionnaireController::getBlockTypes(),
      'questionnaire' => $questionnaire,
      'blocks' => $questionnaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->orderBy('id', 'asc')->get(),
      'chapters' => $chapters,
      'analysis' => $analysis_ordered
    );

    return view('observation.analysis', $data);
  }

  /**
   * post an answer of the analysis
   */
  protected function postAnalysisBlock(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-analysis', $questionnaire);

    $validator = Validator::make($request->all(), [
        'block_id' => 'required|numeric',
        'part' => 'required|numeric',
        'answer' => 'required'
    ]);

    if ($validator->fails()) {
      return Response::json([
        'error' => json_encode($validator)
      ], 400);
    }

    $block = Block::where('id',$request->block_id)->firstOrFail();

    $analysis = Analysis::firstOrNew(['block_id' => $block->id, 'video_id' => $video->id, 'part' => $request->part]);
    $analysis->answer = $request->answer;
    $analysis->save();

    return Response::json([
      'message' => 'Answer saved',
      'analysis' => $analysis
    ], 200);
  }

  /**
   * Process when the analysis is finished
   */
  protected function getAnalysisFinished(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-analysis', $questionnaire);

    $video->analysis = "done";
    $video->save();

    return Redirect::action('Observation\QuestionnaireController@getQuestionnaire', $questionnaire->id)->with('status', 'Finsihed analysis');
  }

  /**
   * Select an export type for the analysis
   */
  protected function getAnalysisExportType($questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-analysis-export', $questionnaire);

    $exportTypes = $this->getAnalysisExportTypes();

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire,
      'exportTypes' => $exportTypes
    );

    return view('observation.analysisExportType', $data);
  }

  /**
   * Validate the export type
   */
  protected function postAnalysisExportType(Request $request, $questionnaire_id, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-analysis-export', $questionnaire);

    $validator = Validator::make($request->all(), [
        'type' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getAnalysisExportType', array('questionnaire_id' => $questionnaire->id, 'video_id' => $video->id))
            ->withInput()
            ->withErrors($validator);
    }

    $exportTypes = $this->getAnalysisExportTypes();

    if(!array_key_exists($request->type, $exportTypes)){
      abort(501, 'Export type not supported');
    }

    return Redirect::action('Observation\VideoController@getAnalysisExport', array('questionnaire_id' => $questionnaire->id, $video->id, $request->type));
  }

  /**
   * export the analysis
   */
  protected function getAnalysisExport($questionnaire_id, $id, $type){
    $video = Video::where('id',$id)->firstOrFail();

    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-analysis-export', $questionnaire);

    $exportTypes = $this->getAnalysisExportTypes();
    if(!array_key_exists($type, $exportTypes)){
      abort(501, 'Export type not supported');
    }

    $export = array();
    $parentBlocks = $questionnaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

    $parts = ceil($video->length/$questionnaire->interval);

    for($part=0 ; $part < $parts ; $part++){
      $export[] = $this->getExportBlocks($parentBlocks, $video, $part);
    }

    return $exportTypes[$type]::exportFile($export, $video, $questionnaire);
  }

  /**
   * Fill the export array from the blocks
   */
  protected function getExportBlocks($blocks, $video, $part){
    $export = array();
    foreach($blocks as $block){
      $blockTypes = QuestionnaireController::getBlockTypes();
      $class = $blockTypes[$block->type];

      $analysis = Analysis::where('video_id', $video->id)->where('block_id', $block->id)->where('part', $part)->get()->first();

      $new = array(
        'text' => $class::getExportName($block),
        'type' => $block->type
      );

      if($analysis){
        $new['answer'] = $class::getAnswerText($analysis->answer, $block);
        $new['score'] = $class::getScore($analysis->answer, $block);
      }

      // get the scores of child blocks
      if($class::canAddChildBlock()){
        $childBlocks = $block->children()->orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        $new['childs'] = $this->getExportBlocks($childBlocks, $video, $part);
      }
      $export[] = $new;
    }
    return $export;
  }
}
