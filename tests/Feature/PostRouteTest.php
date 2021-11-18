<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Post;

class PostRouteTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->runDatabaseMigrations();

        DB::table('posts')->insert([
            'user_id' => 999,
            'content' => 'hello from 999',
            'category' => 'forum',
            'published_at' => '2021-11-18'
        ]);

        DB::table('posts')->insert([
            'user_id' => 7788,
            'content' => 'hello, world',
            'category' => 'forum',
            'published_at' => '2021-11-18'
        ]);
    }

    public function testGetAllPosts_AllPosts()
    {
        $response = $this->json('GET', 'api/posts');

        $response->assertStatus(200);

        $response->assertJsonCount(2);
    }

    public function testGetAllPosts_SpecificAuthor()
    {
        $response = $this->json('GET', 'api/posts?uid=999');

        $response->assertStatus(200);

        $response->assertJsonCount(1);

        $response->assertSeeText('hello from 999');
    }
}
