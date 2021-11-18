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

    private $apiPath = 'api/posts';
    private $postCount;

    public function setUp(): void
    {
        parent::setUp();

        $this->runDatabaseMigrations();

        $this->seedDatabase();
    }

    public function seedDatabase(): void
    {
        $posts = [
            [
                'user_id'      => 999,
                'content'      => 'hello from 999',
                'category'     => 'review',
                'published_at' => '2021-11-18'
            ],
            [
                'user_id'      => 7788,
                'content'      => 'hello, world',
                'category'     => 'forum',
                'published_at' => '2021-11-18'
            ],
            [
                'user_id'      => 7788,
                'content'      => 'amazing case',
                'category'     => 'review',
                'published_at' => '2021-11-18'
            ]
        ];

        $this->postCount = count($posts);

        foreach ($posts as $post) {
            DB::table('posts')->insert($post);
        }
    }

    public function testGetAllPosts_AllPosts(): void
    {
        $response = $this->json('GET', $this->apiPath);

        $response->assertStatus(200);
        $response->assertJsonCount($this->postCount);
    }

    public function testGetAllPosts_SpecificAuthor(): void
    {
        $userID = 999;
        $response = $this->json('GET', $this->apiPath . '?uid=' . $userID);

        $response->assertStatus(200);
        $response->assertSeeText('hello from 999');
    }

    public function testGetAllPosts_SpecificCategory(): void
    {
        $category = 'review';
        $response = $this->json('GET', $this->apiPath . '?category=' . $category);

        $response->assertStatus(200);
        $response->assertSeeText('amazing case');
    }
}
