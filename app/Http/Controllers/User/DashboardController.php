<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  /**
   * Get the dashboard for the user.
   *
   * @return View
   */
  protected function getDashboard()
  {
      return view('user.dashboard');
  }
}
