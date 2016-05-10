<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionnaire;
use App\Models\Analysis;
use App\Models\Video;
use App\Models\Block;
use App\Models\Answer;
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
    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-view', $questionnaire);

    $analyses = Analysis::where('questionnaire_id',$questionnaire_id)->where('video_id', $id)->paginate(15);

    $data = array(
      'analyses' => $analyses,
      'video' => $video,
      'video_types' => $this->getVideoTypes(),
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
    $class::processEditForm($request, $video);

    $video->save();

    return Redirect::action('Observation\VideoController@getVideo', array($questionnaire->id, $video->id))->with('status', 'Video saved');
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
   * Create a new analysis
   */
  protected function postCreateAnalysis($questionnaire_id, $video_id){
    $video = Video::where('id',$video_id)->firstOrFail();
    $questionnaire = $video->questionnaires()->where('questionnaire_id', $questionnaire_id)->get()->first();

    $this->authorize('video-analysis-create', $questionnaire);

    $analysis = new Analysis();
    $analysis->questionnaire_id = $questionnaire_id;
    $analysis->video_id = $video_id;
    $analysis->creator_id = Auth::user()->id;
    $analysis->completed = 0;
    $analysis->save();

    return Redirect::action('Observation\VideoController@getAnalysis', $analysis->id);
  }

  /**
   * Get the analysis of a video
   */
  protected function getAnalysis(Request $request, $id){
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-view', $questionnaire);

    if(!$questionnaire->locked){
      $questionnaire->locked = true;
      $questionnaire->save();
    }

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
    $answers = Answer::where('analysis_id', $analysis->id)->get();
    $answers_ordered = array();
    foreach ($answers as  $row) {
      $answers_ordered[$row->part][$row->block_id] = $row->answer;
    }

    $data = array(
      'analysis' => $analysis,
      'video' => $video,
      'video_types' => $this->getVideoTypes(),
      'block_types' => QuestionnaireController::getBlockTypes(),
      'questionnaire' => $questionnaire,
      'blocks' => $questionnaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->orderBy('id', 'asc')->get(),
      'chapters' => $chapters,
      'answers' => $answers_ordered
    );

    return view('observation.analysis', $data);
  }

  /**
   * post an answer of the analysis
   */
  protected function postAnswerBlock(Request $request, $id){
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-answer', $questionnaire);

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

    $answer = Answer::firstOrNew(['block_id' => $block->id, 'analysis_id' => $analysis->id, 'part' => $request->part]);
    $answer->answer = $request->answer;
    $answer->save();

    return Response::json([
      'message' => 'Answer saved',
      'answer' => $answer
    ], 200);
  }

  /**
   * Process when the analysis is finished
   */
  protected function getAnalysisFinished(Request $request, $id){
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-answer', $questionnaire);

    $analysis->completed = true;
    $analysis->save();

    return Redirect::action('Observation\VideoController@getVideo', array('questionnaire_id' => $questionnaire->id, 'video_id' => $video->id))->with('status', 'Finished analysis');
  }

  /**
   * Select an export type for the analysis
   */
  protected function getAnalysisExportType($id){
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-export', $questionnaire);

    $exportTypes = $this->getAnalysisExportTypes();

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire,
      'exportTypes' => $exportTypes,
      'analysis' => $analysis,
    );

    return view('observation.analysisExportType', $data);
  }

  /**
   * Validate the export type
   */
  protected function postAnalysisExportType(Request $request, $id){
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-export', $questionnaire);

    $validator = Validator::make($request->all(), [
        'type' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getAnalysisExportType', $analysis->id)
            ->withInput()
            ->withErrors($validator);
    }

    $exportTypes = $this->getAnalysisExportTypes();

    if(!array_key_exists($request->type, $exportTypes)){
      abort(501, 'Export type not supported');
    }

    return Redirect::action('Observation\VideoController@getAnalysisExport', array($analysis->id, $request->type));
  }

  /**
   * Get the remove form from a video analysis
   *
   * @return View
   */
  protected function getAnalysisRemove($id)
  {
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-remove', $questionnaire);

    $data = array(
      'video' => $video,
      'questionnaire' => $questionnaire,
      'analysis' => $analysis
    );

    return view('observation.removeAnalysis', $data);
  }

  /**
   * remove an analysis
   *
   * @return Redirect
   */
  protected function postAnalysisRemove(Request $request, $id )
  {
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-remove', $questionnaire);

    $analysis->delete();

    return Redirect::action('Observation\VideoController@getVideo', array($questionnaire->id, $video->id))->with('status', 'Removed analysis');
  }

  /**
   * export the analysis
   */
  protected function getAnalysisExport($id, $type){
    $analysis = Analysis::where('id',$id)->firstOrFail();
    $video = $analysis->video()->get()->first();
    $questionnaire = $analysis->questionnaire()->get()->first();

    $this->authorize('video-analysis-export', $questionnaire);

    $exportTypes = $this->getAnalysisExportTypes();
    if(!array_key_exists($type, $exportTypes)){
      abort(501, 'Export type not supported');
    }

    $export = array();
    $parentBlocks = $questionnaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->orderBy('id', 'asc')->get();

    $parts = ceil($video->length/$questionnaire->interval);

    for($part=0 ; $part < $parts ; $part++){
      $export[] = $this->getExportBlocks($parentBlocks, $analysis, $part);
    }

    return $exportTypes[$type]::exportFile($export, $analysis);
  }

  /**
   * Fill the export array from the blocks
   */
  protected function getExportBlocks($blocks, $analysis, $part){
    $export = array();
    foreach($blocks as $block){
      $blockTypes = QuestionnaireController::getBlockTypes();
      $class = $blockTypes[$block->type];

      $answer = Answer::where('analysis_id', $analysis->id)->where('block_id', $block->id)->where('part', $part)->get()->first();

      $new = array(
        'text' => $class::getExportName($block),
        'type' => $block->type
      );

      if($answer){
        $new['answer'] = $class::getAnswerText($answer->answer, $block);
        $new['score'] = $class::getScore($answer->answer, $block);
      }

      // get the scores of child blocks
      if($class::canAddChildBlock()){
        $childBlocks = $block->children()->orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        $new['childs'] = $this->getExportBlocks($childBlocks, $analysis, $part);
      }
      $export[] = $new;
    }
    return $export;
  }
}
