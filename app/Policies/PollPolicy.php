<?php

namespace App\Policies;

use App\Models\Poll;
use App\Models\User;

class PollPolicy
{
    /**
     * Determine whether the user can view any polls.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether user can view poll.
     */
    public function view(User $user, Poll $poll): bool
    {
        return $poll->team->is($user->currentTeam);
    }

    /**
     * Determine whether the user can create polls.
     */
    public function create(User $user): bool
    {
        if ($user->currentTeam->subscribed()) {
            return true;
        }

        if ($user->currentTeam->hasReachedFreeResponseLimit()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether user can update poll.
     */
    public function update(User $user, Poll $poll): bool
    {
        return $poll->team->is($user->currentTeam);
    }

    /**
     * Determine whether user can delete poll.
     */
    public function delete(User $user, Poll $poll): bool
    {
        return $poll->team->is($user->currentTeam);
    }

    /**
     * Determine whether user can restore poll.
     */
    public function restore(User $user, Poll $poll): bool
    {
        return $poll->team->is($user->currentTeam);
    }

    /**
     * Determine whether user can permanently delete poll.
     */
    public function forceDelete(User $user, Poll $poll): bool
    {
        return $poll->team->is($user->currentTeam);
    }
}
