<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FavoriteResource extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        $posts = $this->collection
            ->where('favorite_type', \App\Models\Post::class)
            ->map(fn ($f) => $f->favorite)
            ->filter()
            ->values();

        $users = $this->collection
            ->where('favorite_type', \App\Models\User::class)
            ->map(fn ($f) => $f->favorite)
            ->filter()
            ->values();

        return [
            'posts' => PostResource::collection($posts),
            'users' => UserResource::collection($users),
        ];
    }
}
