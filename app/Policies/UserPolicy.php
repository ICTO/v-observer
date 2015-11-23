<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the profile.
     *
     * @return bool
     */
    public function profileView(User $user, User $user_profile)
    {
        // if own profile
        if($user->id === $user_profile->id){
            return true;
        }

        // if profile of parent
        $user_groups = $user->groups()->get();
        foreach ($user_groups as $user_group) {
            if($user_group->id === $user_profile->id){
                return true;
            }
        }

        // if profile from same group
        $user_profile_groups = $user_profile->groups()->get();
        foreach ($user_groups as $user_group) {
            foreach($user_profile_groups as $user_profile_group){
                if($user_group->id === $user_profile_group->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if user can view the profile.
     *
     * @return bool
     */
    public function profileEdit(User $user, User $user_profile)
    {
        // if own profile
        if($user->id === $user_profile->id){
            return true;
        }

        // @TODO : if admin of group

        return false;
    }

    /**
     * Determine if user can view the dashboard of a profile.
     *
     * @return bool
     */
    public function dashboard(User $user, User $user_profile)
    {
        return $this->profileView($user,$user_profile);
    }

    /**
     * Determine if user can create a user in a group.
     *
     * @return bool
     */
    public function userCreate(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Determine if user can create a user in a group.
     *
     * @return bool
     */
    public function userAdd(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Determine if user can remove a user in a group.
     *
     * @return bool
     */
    public function userRemove(User $user, User $group)
    {
        return $this->isGroupAdmin($user, $group);
    }

    /**
     * Helper function to determin if the user is admin of the group.
     */
    private function isGroupAdmin(User $user, User $group){
        // @TODO : this can be more efficient without the loop.
        $user_groups = $user->groups()->get();
        foreach ( $user_groups as $user_group) {
            if($user_group->id === $group->id ){
                if($user_group->pivot->admin){
                    return true;
                }
            }
        }
        return false;
    }
}
