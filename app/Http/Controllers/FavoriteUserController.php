<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFavoriteRequest;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FavoriteUserController extends Controller
{
    public function store(CreateFavoriteRequest $request, User $user)
    {
        $type = get_class($user);

        $user_to_favorite = $user->id;
        $user_favoriting = $request->user()->id;

        if ($user_to_favorite === $user_favoriting) {
            return response()->json(['message' => 'You cannot favorite yourself.'], ResponseAlias::HTTP_BAD_REQUEST);
        }

        Favorite::firstOrCreate(['favorite_type' => $type, 'favorite_id' => $user_to_favorite, 'user_id' => $user_favoriting]);

        return response()->noContent(ResponseAlias::HTTP_CREATED);
    }

    public function destroy(Request $request, User $user)
    {
        $type = get_class($user);

        $favorite = Favorite::where('favorite_type', $type)->where('favorite_id', $user->id)->firstOrFail();

        $favorite->delete();

        return response()->noContent();
    }
}
