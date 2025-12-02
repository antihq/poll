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
     * Determine whether the user can view the poll.
     */
    public function view(User $user, Poll $poll): bool
    {
        return $poll->organization->is($user->currentOrganization);
    }

    /**
     * Determine whether the user can create polls.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the poll.
     */
    public function update(User $user, Poll $poll): bool
    {
        return $poll->organization->is($user->currentOrganization);
    }

    /**
     * Determine whether the user can delete the poll.
     */
    public function delete(User $user, Poll $poll): bool
    {
        return $poll->organization->is($user->currentOrganization);
    }

    /**
     * Determine whether the user can restore the poll.
     */
    public function restore(User $user, Poll $poll): bool
    {
        return $poll->organization->is($user->currentOrganization);
    }

    /**
     * Determine whether the user can permanently delete the poll.
     */
    public function forceDelete(User $user, Poll $poll): bool
    {
        return $poll->organization->is($user->currentOrganization);
    }
}
