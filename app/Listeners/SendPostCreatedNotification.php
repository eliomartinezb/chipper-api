<?php

namespace App\Listeners;

use App\Events\PostCreated;
use App\Models\Favorite;
use App\Models\User;
use App\Notifications\NewPostFromFavoritedUser;
use Illuminate\Support\Facades\Notification;

class SendPostCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post;
        $authorId = $post->user_id;

        $followers = User::query()
            ->whereIn('id', Favorite::query()
                ->where('favorite_type', User::class)
                ->where('favorite_id', $authorId)
                ->pluck('user_id')
            )->get();

        Notification::send(
            $followers,
            new NewPostFromFavoritedUser($post)
        );
    }
}
