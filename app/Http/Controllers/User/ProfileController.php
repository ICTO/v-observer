<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Illuminate\Http\Request;
use Redirect;

class ProfileController extends Controller
{
  /**
   * Get the users profile.
   *
   * @return View
   */
  protected function getProfile()
  {
      $data = array(
        'user'=> Auth::user()
      );
      return view('user.profile', $data);
  }
  /**
   * Get the users profile edit form.
   *
   * @return View
   */
  protected function getEditProfile()
  {
      $data = array(
        'user'=> Auth::user()
      );
      return view('user.editProfile', $data);
  }

  /**
   * Get the users profile edit form.
   *
   * @return View
   */
  protected function postEditProfile(Request $request)
  {
      $user = Auth::user();

      $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required|email|unique:users,email,' . $user->id
      ]);

      if ($validator->fails()) {
          return Redirect::action('User\ProfileController@getEditProfile')
              ->withInput()
              ->withErrors($validator);
      }

      $user->name = $request->name;
      $user->email = $request->email;
      $user->save();

      return Redirect::action('User\ProfileController@getProfile')->with('status', 'Saved profile');
  }
}
