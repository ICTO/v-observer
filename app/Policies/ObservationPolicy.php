<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Questionaire;
use App\Models\User;

class ObservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can view the questionaire.
     *
     * @return bool
     */
    public function QuestionaireView(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Determine if user can edit the questionaire.
     *
     * @return bool
     */
    public function QuestionaireEdit(User $user, Questionaire $questionaire)
    {
        // if own profile
        if($questionaire->owner_id === $user->id){
            return true;
        }

        if($this->isGroupAdmin($user, $questionaire->owner()->get()->first())){
            return true;
        }

        return false;
    }

    /**
     * Determine if user can remove the questionaire.
     *
     * @return bool
     */
    public function QuestionaireRemove(User $user, Questionaire $questionaire)
    {
        return $this->QuestionaireEdit($user, $questionaire);
    }

    /**
     * Helper function to determin if the user is admin of the group.
     */
    private function isGroupAdmin(User $user, User $group){
        $user_group = $user->groups()->where('id', $group->id)->get();
        if(!$user_group->isEmpty() && $user_group->first()->pivot->role == 'admin'){
            return true;
        }

        return false;
    }
}