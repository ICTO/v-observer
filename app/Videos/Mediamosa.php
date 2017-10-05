<?php

namespace App\Videos;

use App\Videos\VideoInterface;
use Validator;
use Redirect;
use Auth;

class Mediamosa implements VideoInterface{

  /**
   * {@inheritdoc}
   */
  static function validatorCreateForm($request){
    return Validator::make($request->all(), [
        'name' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function validatorEditForm($request){
    return Validator::make($request->all(), [
        'name' => 'required'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  static function processCreateForm($request, $video){
    $mmc = new MediamosaConnector;

    $user = Auth::user();

    $response = $mmc->createAsset($user->id);
    if(empty($response['data']['items']['item'][0]['asset_id'])){
      abort(500, 'Mediamosa: Failed creating asset');
    }
    $asset_id = $response['data']['items']['item'][0]['asset_id'];
    $data = array(
      'isprivate' => 'true'
    );
    $response = $mmc->updateAsset($asset_id, $user->id, $data);
    if(empty($response)){
      abort(500, 'Mediamosa: Failed updating asset');
    }
    $response = $mmc->createMediafile($asset_id, $user->id);
    if(empty($response['data']['items']['item'][0]['mediafile_id'])){
      abort(500, 'Mediamosa: Failed creating mediafile');
    }
    $mediafile_id = $response['data']['items']['item'][0]['mediafile_id'];

    $response = $mmc->createUploadTicket($mediafile_id, $user->id, $_SERVER['HTTP_REFERER']);
    if(empty($response['data']['items']['item'][0]['action'])){
      abort(500, 'Mediamosa: Failed creating upload ticket');
    }

    $action = $response['data']['items']['item'][0]['action'];
    $uploadprogress_url = $response['data']['items']['item'][0]['uploadprogress_url'];
    $ticket_id = $response['data']['items']['item'][0]['ticket_id'];
    $progress_id = $response['data']['items']['item'][0]['progress_id'];

    if(!empty($_SERVER['HTTPS'])){
      $uploadprogress_url = str_replace("http://", "https://", $uploadprogress_url);
      $action = str_replace("http://", "https://", $action);
    }

    $random_id = $mmc->generateRandomString(8);

    $video->data = array(
      'status' => 'uploadticket',
      'asset_id' => $asset_id,
      'mediafile_id' => $mediafile_id,
      'uploadticket_data' => array(
        'action' => $action,
        'uploadprogress_url' => $uploadprogress_url,
        'ticket_id' => $ticket_id,
        'progress_id' => $progress_id,
        'random_id' => $random_id
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  static function processRemoveForm($request, $video){
    // soft removes in database so don't remove the file in mediamosa for recovery

    // Remove file from mediamosa
    // $asset_id = $video->data['asset_id'];
    // $user_id = $video->creator_id;
    // $mmc = new MediamosaConnector;
    // $mmc->deleteAsset($asset_id, $user_id);
  }

  /**
   * {@inheritdoc}
   */
  static function processEditForm($request, $video){
    return array();
  }

  /**
   * {@inheritdoc}
   */
  static function getHumanName(){
    return 'Mediamosa video';
  }

  /**
   * {@inheritdoc}
   */
  static function uploadFinished($request, $video){
    $newData = $video->data;
    $newData['status'] = 'processing';
    unset($newData['uploadticket_data']);
    $video->data = $newData;
    $video->save();
    return array(
      'status' => 'Upload finished'
    );
  }

  /**
   * {@inheritdoc}
   */
  static function uploadProgress($request, $questionnaire, $video){
    if($video->data['status'] == 'uploadticket'){
      $url = $video->data['uploadticket_data']['uploadprogress_url'];
      // change id in the url @TODO : is this needed????
      $url_parts = explode("=", $url);
      array_pop($url_parts);
      $url = implode("=", $url_parts) . "=" . $video->data['uploadticket_data']['random_id'];

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      $output = curl_exec($ch);
      $data = json_decode($output);
      curl_close($ch);

      if(empty($data)){
        return array(
          'loaders' => array(
            array(
              'name' => "Error connecting to Mediamosa",
            ),
          ),
          'redirect' => false,
        );
      }

      if($data->percentage == "-1"){
        $name = 'Uploading file. This may take a while depending on the size of the file.';
        $percentage = false;
      } else {
        $name = 'UPLOADING ('.$data->percentage.'%)';
        $percentage = $data->percentage;
      }

      return array(
        'loaders' => array(
          array(
            'name' => $name,
            'percentage' => $percentage
          ),
        ),
        'redirect' => false,
      );
    }

    if($video->data['status'] == 'processing'){
      $mmc = new MediamosaConnector();
      $data = $mmc->getJobs($video->data['mediafile_id'], $video->creator_id);
      $loaders = array();
      $done = true;
      foreach ($data['data']['items']['item'] as $item) {
        $loaders[] = array(
          'name' => $item['job_type'] . " (".$item['status'].")",
          'percentage' => round($item['progress']*100)
        );
        if($item['status'] != "FINISHED" || !($item['job_type'] == 'STILL' && $item['status'] == 'FAILED')){ // STILL creation somethimes fails so we add an exception because we don't use it.
          $done = false;
        }
      }

      $response = array(
        'loaders' => $loaders,
        'redirect' => $done ? action('Observation\VideoController@getVideo', array($questionnaire->id, $video->id)) : false
      );

      if($done){
        $r = $mmc->getMediafile($video->data['mediafile_id']);
        $video->size = $r['data']['items']['item'][0]['metadata']['filesize'];
        $length = explode(":", $r['data']['items']['item'][0]['metadata']['file_duration']);
        $video->length = round((integer) $length[2] + ((integer) $length[1]*60) + ((integer) $length[0]*3600) );
        $vdata = $video->data;
        $vdata['status'] = "ready";
        $video->data = $vdata;
        $video->save();
      }

      return $response;
    }
  }

  /**
   * Get all mediafile urls
   */
  static function getVideoSources($asset_id){
    $mmc = new MediamosaConnector();
    $response = $mmc->getAsset($asset_id);
    if(isset($response['data']['items']['item'][0]['mediafiles']['mediafile']['mediafile_id'])){
        $response['data']['items']['item'][0]['mediafiles']['mediafile'] = array($response['data']['items']['item'][0]['mediafiles']['mediafile']);
    }
    $urls = array();
    if(!empty($response['data']['items']['item'][0]['mediafiles']['mediafile'])){
      foreach($response['data']['items']['item'][0]['mediafiles']['mediafile'] as $mediafile){
        $resp = $mmc->getMediafilePlayURL($mediafile['mediafile_id'], $asset_id, $mediafile['owner_id'], 'plain');
        $urls[] = $resp['data']['items']['item'][0];
      }
    }
    return $urls;
  }
}


/**
 * class to connect to mediamosa
 */
class MediamosaConnector {
  protected $enabled;
  protected $server_url;
  protected $client_name;
  protected $client_pw;
  protected $session;


  /**
   * Initiate the connection settings
   */
  public function __construct() {
    $this->server_url = env('MEDIAMOSA_URL');
    $this->client_name = env('MEDIAMOSA_CLIENT');
    $this->client_pw = env('MEDIAMOSA_PASSWORD');
    $this->login();
  }

  /**
   * Get the status
   */
  public function getStatus() {
    return $this->doRequest("status");
  }

  /**
   * get jobs
   *
   * Function to get the jobs of a mediafile
   */
  public function getJobs($mediafile_id, $user_id) {
    $data = array(
      'user_id' => $user_id,
    );
    return $this->doRequest("/mediafile/".$mediafile_id."/joblist", 'GET', $data);
  }

  /**
   * create asset
   *
   * Function to create a new asset, returns new asset id
   */
  public function createAsset($user_id) {
    $data = array(
      'user_id' => $user_id,
    );
    return $this->doRequest('asset/create', 'POST', $data);
  }

  /**
   * Update asset
   */
  public function updateAsset($asset_id, $user_id, $data){
    $data['user_id'] = $user_id;
    return $this->doRequest('asset/'.$asset_id, 'POST', $data);
  }

  /**
   * delete asset
   */
  public function deleteAsset($asset_id, $user_id){
    $data = array();
    $data['user_id'] = $user_id;
    $data['delete'] = "cascade";
    return $this->doRequest('asset/' . $asset_id . "/delete", 'POST', $data);
  }

  /**
   * save metadata
   */
  public function saveMetadata($asset_id, $user_id, $data){
    $data['user_id'] = $user_id;
    return $this->doRequest('asset/' . $asset_id . "/metadata", 'POST', $data);
  }

  /**
   * create mediafile
   * This function creates a mediafile and returns the mediafile id
   */
  public function createMediafile($asset_id, $user_id) {
    $data = array(
        'asset_id' => $asset_id,
        'user_id' => $user_id,
    );
    return $this->doRequest('/mediafile/create', 'POST', $data);
  }

  /**
   * create upload ticket
   */
  public function createUploadTicket($mediafile_id, $user_id, $return_url)
  {
    if(empty($mediafile_id)){
      return false;
    }

    $data = array(
      'mediafile_id' => $mediafile_id,
      'user_id' => $user_id
    );

    return $this->doRequest('/mediafile/' .$mediafile_id .'/uploadticket/create', 'POST', $data);
  }

  /**
   * Count the assets
   */
  public function getAssets($data = FALSE){
    return $this->doRequest("asset", 'GET', $data);
  }

  /**
   * Get media file
   */
  public function getAsset($asset_id){
    return $this->doRequest("asset/$asset_id");
  }

  /**
   * Get media file
   */
  public function getMediafile($mediafile_id){
    return $this->doRequest("mediafile/$mediafile_id");
  }

  /**
   * Get asset play url
   */
  public function getMediafilePlayURL($mediafile_id, $asset_id, $user_id, $response = 'plain'){
    $data = array(
      'user_id' => $user_id,
      'asset_id' => $asset_id,
      'mediafile_id' => $mediafile_id,
      'response' => $response,
    );
    return $this->doRequest("asset/$asset_id/play", 'GET', $data);
  }

  /**
   * Get the status
   */
  public function login(){
    // Step 1, set the encoding of the handshake and receive the challange
    $data = array(
      'dbus' => 'AUTH DBUS_COOKIE_SHA1 ' . $this->client_name,
    );
    $response = $this->doRequest("login", 'POST', $data);

    if (substr($response['data']['items']['item'][0]['dbus'], 0, 5) != 'DATA ') {
      return FALSE;
    }

    $dbus_data = explode(' ', $response['data']['items']['item'][0]['dbus']);

    // Step 2: Do challenge.
    $challenge = $dbus_data[3];
    $random = substr(md5(microtime(TRUE)), 0, 10);
    $challenge_response = sha1(sprintf('%s:%s:%s', $challenge, $random, $this->client_pw));
    $data = array('dbus' => sprintf('DATA %s %s', $random, $challenge_response));
    $response = $this->doRequest("login", 'POST', $data);

    if (!isset($response['data']['items']['item'][0]['dbus'])) {
        return FALSE;
    }

    // Lets check if its ok.
    return substr($response['data']['items']['item'][0]['dbus'], 0, 2) == 'OK';
  }

  /**
   * Do curl request and return the output.
   */
  private function doRequest($url, $type='GET', $data = FALSE){
    // check if curl is installed.
    if (!function_exists('curl_init')){
      throw new \Exception('Sorry cURL is not installed!');
    }

    $ch = curl_init();
    switch ($type) {
      case 'GET':
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        if($data){
          $url .= "?" . http_build_query($data);
        }
      break;

      case 'POST':
        curl_setopt($ch, CURLOPT_HTTPGET, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        if($data) {
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
      break;
    }

    curl_setopt($ch, CURLOPT_USERAGENT, 'Curios');
    curl_setopt($ch, CURLOPT_URL, $this->server_url . $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    // keep the session
    if(!empty($this->session)){
      curl_setopt( $ch, CURLOPT_COOKIE, $this->session );
    }

    $output = curl_exec($ch);

    // Close the cURL resource, and free system resources
    curl_close($ch);

    // split header and body
    $parts = explode("\r\n\r\n", $output);
    $count_parts = count($parts);
    if($count_parts < 2){
      return FALSE;
    }
    $body = $parts[$count_parts-1];
    $header = $parts[$count_parts-2];
    $header = preg_split("/\r\n|\n|\r/", $header);

    // Parse the response headers.
    $headers = array();
    while ($line = trim(array_shift($header))) {
      $header_parts = explode(':', $line, 2);
      if(count($header_parts) == 2){
        $name = $header_parts[0];
        $value = $header_parts[1];

        $name = strtolower($name);
        if (isset($headers[$name]) && $name == 'set-cookie') {
          // RFC 2109: the Set-Cookie response header comprises the token Set-
          // Cookie:, followed by a comma-separated list of one or more cookies.
          $headers[$name] .= ',' . trim($value);
        } else {
          $headers[$name] = trim($value);
        }
      }
    }

    if(!empty($headers['set-cookie'])){
      $this->session = $headers['set-cookie'];
    }

    // make a php array of the xml code from the body
    $body = simplexml_load_string($body);
    $body = json_encode($body);
    $body = json_decode($body,TRUE);

    if($body['header']['request_result'] != 'success'){
      //var_dump($body);
      //echo 'Error: ' . $body['header']['request_result_description'];
      return FALSE;
    }

    // if only 1 item, make an array.
    if($body['header']['item_count'] == 1){
      $body['items']['item'] = array($body['items']['item']);
    }

    return array(
      'header' => $headers,
      'data' => $body,
    );
  }

  /**
   * Generate a random string to use for the porgress bar
   * @param  integer $length the length of the random string
   * @return string          random string
   */
  function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
  }
}

?>
