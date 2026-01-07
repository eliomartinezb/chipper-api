<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFavoriteRequest;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
 * @group Favorites
 *
 * API endpoints for managing favorites
 */
class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->with('favoritable')->get();

        return new FavoriteResource($favorites);
    }

    public function store(CreateFavoriteRequest $request, Post $post)
    {
        $type = get_class($post);

        Favorite::firstOrCreate(['favorite_type' => $type, 'favorite_id' => $post->id, 'user_id' => $request->user()->id]);

        return response()->noContent(ResponseAlias::HTTP_CREATED);
    }

    public function destroy(Request $request, Post $post)
    {
        $type = get_class($post);

        $favorite = Favorite::where('favorite_type', $type)->where('favorite_id', $post->id)->firstOrFail();

        $favorite->delete();

        return response()->noContent();
    }
}
