<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Questionaire;
use App\Models\Video;
use App\Models\Block;
use App\Models\Analysis;
use Validator;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Redirect;
use Response;
use App\Http\Controllers\Observation\QuestionaireController;

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
  protected function getVideo($id)
  {
    $video = Video::where('id',$id)->firstOrFail();

    $video_types = $this->getVideoTypes();

    $data = array(
      'video' => $video,
      'video_types' => $video_types,
      'questionaire' => $video->questionaire()->get()->first()
    );

    return view('observation.videoDisplay', $data);
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

    return view('observation.videos.'.$video->type.'.create', $data);
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
    $video->analysis = "no";

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$type];

    $validator = $class::validatorCreateForm($request);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getCreateVideo', array($questionaire->id, $type) )
            ->withInput()
            ->withErrors($validator);
    }

    $video->name = $request->name;
    $class::processCreateForm($request, $video);

    $video->save();

    return Redirect::action('Observation\VideoController@getVideo', $video->id);
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

    return view('observation.videos.'.$video->type.'.edit', $data);
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
    $class::processEditForm($request);

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

    return view('observation.videos.'.$video->type.'.remove', $data);
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

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    $class::processRemoveForm($request, $video);

    $video->delete();

    return Redirect::action('Observation\QuestionaireController@getQuestionaire', $video->questionaire_id)->with('status', 'Removed video');
  }

  /**
   * Process when the upload is finished
   */
  protected function getUploadFinished(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-edit', $questionaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    return response()->json($class::uploadFinished($request, $video));
  }

  /**
   * Get the upload progress
   */
  protected function getUploadProgress(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-edit', $questionaire);

    $videoTypes = $this->getVideoTypes();
    $class = $videoTypes[$video->type];

    return response()->json($class::uploadProgress($request, $video));
  }

  /**
   * Get the edit form for the transcript of a video
   */
  protected function getEditTranscript(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-edit-transcript', $questionaire);

    $data = array(
      'video' => $video,
    );

    return view('observation.editTranscript', $data);

  }

  /**
   * Process the edit form for the transcript of a video
   */
  protected function postEditTranscript(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-edit-transcript', $questionaire);

    $video->transcript = $request->transcript;
    $video->save();

    return Redirect::action('Observation\VideoController@getVideo', $video->id);
  }

  /**
   * Get the edit form for the transcript of a video
   */
  protected function getAnalysis(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    if(!$questionaire->locked){
      $questionaire->locked = true;
      $questionaire->save();
    }

    if($video->analysis == "no"){
      $video->analysis = "running";
      $video->save();
    }

    $this->authorize('video-analysis', $questionaire);

    $intervals = array();
    $position = 0;
    $end = 0;

    while($position < $video->length){
      $start = $position;
      $end += $questionaire->interval;
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
      'block_types' => QuestionaireController::getBlockTypes(),
      'questionaire' => $questionaire,
      'blocks' => $questionaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->get(),
      'chapters' => $chapters,
      'analysis' => $analysis_ordered
    );

    return view('observation.analysis', $data);
  }

  /**
   * post an answer of the analysis
   */
  protected function postAnalysisBlock(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-analysis', $questionaire);

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
  protected function getAnalysisFinished(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-analysis', $questionaire);

    $video->analysis = "done";
    $video->save();

    return Redirect::action('Observation\QuestionaireController@getQuestionaire', $questionaire->id)->with('status', 'Finsihed analysis');
  }

  /**
   * Select an export type for the analysis
   */
  protected function getAnalysisExportType($id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-analysis-export', $questionaire);

    $exportTypes = $this->getAnalysisExportTypes();

    $data = array(
      'video' => $video,
      'questionaire' => $questionaire,
      'exportTypes' => $exportTypes
    );

    return view('observation.analysisExportType', $data);
  }

  /**
   * Validate the export type
   */
  protected function postAnalysisExportType(Request $request, $id){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-analysis-export', $questionaire);

    $validator = Validator::make($request->all(), [
        'type' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('Observation\VideoController@getAnalysisExportType', $video->id)
            ->withInput()
            ->withErrors($validator);
    }

    $exportTypes = $this->getAnalysisExportTypes();

    if(!array_key_exists($request->type, $exportTypes)){
      abort(501, 'Export type not supported');
    }

    return Redirect::action('Observation\VideoController@getAnalysisExport', array($video->id, $request->type));
  }

  /**
   * export the analysis
   */
  protected function getAnalysisExport($id, $type){
    $video = Video::where('id',$id)->firstOrFail();

    $questionaire = $video->questionaire()->get()->first();

    $this->authorize('video-analysis-export', $questionaire);

    $exportTypes = $this->getAnalysisExportTypes();
    if(!array_key_exists($type, $exportTypes)){
      abort(501, 'Export type not supported');
    }

    $export = array();
    $parentBlocks = $questionaire->blocks()->whereNull('parent_id')->orderBy('order', 'asc')->get();

    $parts = ceil($video->length/$questionaire->interval);

    for($part=0 ; $part < $parts ; $part++){
      $export[] = $this->getExportBlocks($parentBlocks, $video, $part);
    }

    return $exportTypes[$type]::exportFile($export, $video, $questionaire);
  }

  /**
   * Fill the export array from the blocks
   */
  protected function getExportBlocks($blocks, $video, $part){
    $export = array();
    foreach($blocks as $block){
      $blockTypes = QuestionaireController::getBlockTypes();
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
        $childBlocks = $block->children()->orderBy('order', 'asc')->get();
        $new['childs'] = $this->getExportBlocks($childBlocks, $video, $part);
      }
      $export[] = $new;
    }
    return $export;
  }
}
