<?php

namespace App\Http\Controllers\Observation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect;

class VideoController extends Controller
{

  /**
   * Video types
   */
  private function getVideoTypes(){
    return array(
      'Mediamosa' => '\App\Videos\Mediamosa',
    );
  }
}
