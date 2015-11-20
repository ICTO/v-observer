<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\User;
use Auth;
use Validator;
use Illuminate\Http\Request;
use Redirect;
use Mail;
use \Illuminate\Auth\Passwords\TokenRepositoryInterface;

class UserController extends Controller
{

  protected $tokens;

  public function __construct(TokenRepositoryInterface $tokens)
  {
      $this->tokens = $tokens;
  }

  /**
   * Get the dashboard.
   *
   * @return View
   */
  protected function getDashboard($id = false)
  {
    if($id){
      $user = User::where('id',$id)->firstOrFail();
    } else {
      $user = Auth::user();
    }

    $data = array(
      'user' => $user,
      'users' => $user->users()->get(),
      'groups' => $user->groups()->get(),
    );

    return view('user.dashboard', $data);
  }

  /**
   * Get the users profile.
   *
   * @return View
   */
  protected function getProfile($id = false)
  {
    if($id){
      $user = User::where('id',$id)->firstOrFail();
    } else {
      $user = Auth::user();
    }

    $data = array(
      'user'=> $user
    );
    return view('user.profile', $data);
  }
  /**
   * Get the users profile edit form.
   *
   * @return View
   */
  protected function getEditProfile($id)
  {
    $user = User::where('id',$id)->firstOrFail();

    $data = array(
      'user'=> $user
    );
    return view('user.editProfile', $data);
  }

  /**
   * Get the users profile edit form.
   *
   * @return View
   */
  protected function postEditProfile(Request $request, $id)
  {
    $user = User::where('id',$id)->firstOrFail();

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'email|unique:users,email,' . $user->id,
        'cas_username' => 'unique:users,cas_username,' . $user->id
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getEditProfile', $user->id)
            ->withInput()
            ->withErrors($validator);
    }

    $user->name = $request->name;
    $user->email = $request->email;
    $user->cas_username = $request->cas_username;
    $user->save();

    return Redirect::action('User\UserController@getProfile', $user->id)->with('status', 'Saved profile');
  }

  /**
   * Get the groups of a user.
   *
   * @return View
   */
  protected function getGroups()
  {
      $user = Auth::user();

      $data = array(
        'groups' => $user->groups()->get(),
      );

      return view('user.groups', $data);
  }

  /**
   * Return the form for creating a new group.
   *
   * @return View
   */
  protected function getCreateGroup()
  {
      return view('user.createGroup');
  }

  /**
   * Create and validate a new group.
   *
   * @return View
   */
  protected function postCreateGroup(Request $request)
  {
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'name' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getCreateGroup')
            ->withInput()
            ->withErrors($validator);
    }

    $group = new User();
    $group->name = $request->name;
    $group->save();
    $group->users()->attach($user->id);

    return Redirect::action('User\UserController@getGroups')->with('status', 'Group created');
  }

  /**
   * Return the form for creating a new user.
   *
   * @return View
   */
  protected function getCreateUser($group_id = false)
  {
    $data = array(
      'group_id' => $group_id
    );

    return view('user.createUser', $data);
  }

  /**
   * Create and validate a new user.
   *
   * @return View
   */
  protected function postCreateUser(Request $request)
  {
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'cas_username' => 'unique:users,cas_username'
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getCreateUser', $request->group_id)
            ->withInput()
            ->withErrors($validator);
    }

    $new_user = new User();
    $new_user->name = $request->name;
    $new_user->email = $request->email;
    $new_user->cas_username = $request->cas_username;
    $new_user->save();
    // @TODO : check if group_id is one of the allowed ids.
    if($request->group_id){
      $new_user->groups()->attach($request->group_id);
    }

    if($request->send_email){
      $token = $this->tokens->create($new_user);
      $data = array(
        'token' => $token,
        'new_user' => $new_user,
        'user' => $user
      );
      Mail::send('emails.newUser', $data, function($message) use ($new_user, $user){
          $message->to($new_user->email, $new_user->name)->subject('Invitation from ' . $user->name);
      });
    }

    if($request->group_id){
      return Redirect::action('User\UserController@getDashboard', $request->group_id)->with('status', 'User created');
    }

    return Redirect::action('User\UserController@getProfile', $new_user->id)->with('status', 'User created');
  }

  /**
   * Return the form for adding a user to a group.
   *
   * @return View
   */
  protected function getAddUser($id)
  {
    $group = User::where('id',$id)->firstOrFail();
    $users = User::all();

    $data = array(
      'group' => $group,
      'users' => $users
    );

    return view('user.addUser', $data);
  }

  /**
   * Add and validate a user that needs to be added to a group
   *
   * @return View
   */
  protected function postAddUser(Request $request)
  {
    // @TODO : validate that the user is only added once to a group
    $validator = Validator::make($request->all(), [
        'group_id' => 'required',
        'user_id' => 'required'
    ]);

    if ($validator->fails()) {
        return Redirect::action('User\UserController@getAddUser', $id)
            ->withInput()
            ->withErrors($validator);
    }

    User::where('id',$request->user_id)->firstOrFail();
    $group = User::where('id',$request->group_id)->firstOrFail();
    $group->users()->attach($request->user_id);

    // @TODO : send a mail to the new user and notice him that he is added to the group

    return Redirect::action('User\UserController@getDashboard', $group->id)->with('status', 'User added to group');
  }
}
