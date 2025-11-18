<?php

namespace App\Policies;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FeedbackPolicy
{
    public function view(User $user, Feedback $feedback): Response
    {
        return $user->id === $feedback->sender_id || 
               $user->id === $feedback->receiver_id ||
               $user->hasRole('hr') ||
               $user->hasRole('manager')
            ? Response::allow()
            : Response::deny('You do not have permission to view this feedback.');
    }

    public function update(User $user, Feedback $feedback): Response
    {
        return $user->id === $feedback->sender_id && 
               $feedback->status === 'draft'
            ? Response::allow()
            : Response::deny('You can only update draft feedback that you created.');
    }

    public function delete(User $user, Feedback $feedback): Response
    {
        return $user->id === $feedback->sender_id && 
               $feedback->status === 'draft'
            ? Response::allow()
            : Response::deny('You can only delete draft feedback that you created.');
    }

    public function respond(User $user, Feedback $feedback): Response
    {
        return $user->id === $feedback->receiver_id
            ? Response::allow()
            : Response::deny('Only the feedback recipient can respond.');
    }
}