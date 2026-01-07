<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_guest_can_not_favorite_a_post()
    {
        $post = Post::factory()->create();

        $this->postJson(route('favorites.store', ['post' => $post]))
            ->assertStatus(401);
    }

    public function test_a_user_can_favorite_a_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.store', ['post' => $post]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'favorite_type' => get_class($post),
            'favorite_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_a_user_can_remove_a_post_from_his_favorites()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.store', ['post' => $post]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'favorite_type' => get_class($post),
            'favorite_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('favorites.destroy', ['post' => $post]))
            ->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'favorite_type' => get_class($post),
            'favorite_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_a_user_can_not_remove_a_non_favorited_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('favorites.destroy', ['post' => $post]))
            ->assertNotFound();
    }
    public function test_a_user_can_favorite_an_user()
    {
        $user = User::factory()->create();
        $favoritable = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.users.store', ['user' => $favoritable]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'favorite_type' => get_class($favoritable),
            'favorite_id' => $favoritable->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_a_user_cannot_favorite_himself()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.users.store', ['user' => $user]))
            ->assertBadRequest();

        $this->assertDatabaseMissing('favorites', [
            'favorite_type' => get_class($user),
            'favorite_id' => $user->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_a_user_can_remove_another_user_from_his_favorites()
    {
        $user = User::factory()->create();
        $favoritable = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('favorites.users.store', ['user' => $favoritable]))
            ->assertCreated();

        $this->assertDatabaseHas('favorites', [
            'favorite_type' => get_class($favoritable),
            'favorite_id' => $favoritable->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->deleteJson(route('favorites.users.destroy', ['user' => $favoritable]))
            ->assertNoContent();

        $this->assertDatabaseMissing('favorites', [
            'favorite_type' => get_class($favoritable),
            'favorite_id' => $favoritable->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_a_user_can_not_remove_a_non_favorited_user()
    {
        $user = User::factory()->create();
        $favoritable = User::factory()->create();

        $this->actingAs($user)
            ->deleteJson(route('favorites.users.destroy', ['user' => $favoritable]))
            ->assertNotFound();
    }
}
