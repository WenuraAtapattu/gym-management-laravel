<?php

namespace App\Observers;

use App\Models\MongoUser;
use Illuminate\Support\Str;

class MongoUserObserver
{
    /**
     * Handle the MongoUser "creating" event.
     */
    public function creating(MongoUser $user): void
    {
        // Generate a unique API token for the user
        $user->setAttribute('api_token', Str::random(60));
    }

    /**
     * Handle the MongoUser "updating" event.
     */
    public function updating(MongoUser $user): void
    {
        // Regenerate API token if email is being updated
        if ($user->isDirty('email')) {
            $user->setAttribute('api_token', Str::random(60));
        }
    }
}
