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

    private $DUMMY_CONTENT = "lorem ipsum";
    private $DUMMY_CATEGORY = "doesn't matter";

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
            ],
            [
                'user_id'      => 11,
                'content'      => $this->DUMMY_CONTENT,
                'category'     => $this->DUMMY_CATEGORY,
                'published_at' => '2021-10-18'
            ],
            [
                'user_id'      => 12,
                'content'      => $this->DUMMY_CONTENT,
                'category'     => $this->DUMMY_CATEGORY,
                'published_at' => '2021-09-18'
            ],
            [
                'user_id'      => 13,
                'content'      => $this->DUMMY_CONTENT,
                'category'     => $this->DUMMY_CATEGORY,
                'published_at' => '2021-09-18'
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

    public function testGetAllPosts_NoResult(): void
    {
        $response = $this->json('GET', $this->apiPath . '?uid=1023');

        $response->assertStatus(200);
        $response->assertJsonCount(0);
    }

    public function testGetAllPosts_SpecificAuthor(): void
    {
        $userID = 999;
        $response = $this->json('GET', $this->apiPath . '?uid=' . $userID);

        $response->assertStatus(200);

        foreach ($response->json() as $post) {
            $this->assertEquals($userID, $post['user_id']);
        }
    }

    public function testGetAllPosts_SpecificCategory(): void
    {
        $category = 'review';
        $response = $this->json('GET', $this->apiPath . '?category=' . $category);

        $response->assertStatus(200);

        foreach ($response->json() as $post) {
            $this->assertEquals($category, $post['category']);
        }
    }

    public function testGetAllPosts_ContainContent(): void
    {
        $keyword = 'hello';
        $response = $this->json('GET', $this->apiPath . '?search=' . $keyword);

        $response->assertJsonCount(2);
        $response->assertStatus(200);
        $response->assertSeeText('hello');

        foreach ($response->json() as $post) {
            $this->assertStringContainsString($keyword, $post['content']);
        }
    }

    public function testGetAllPosts_BetweenDate(): void
    {
        $startDate = '2021-01-01';
        $endDate = '2021-10-31';
        $response = $this->json('GET', $this->apiPath . '?startdate=' . $startDate . '&enddate=' . $endDate);

        $response->assertJsonCount(3);
        $response->assertStatus(200);
    }

    public function testGetAllPosts_SpecificDateAndUserID_get1Post(): void
    {
        $startDate = '2021-01-01';
        $endDate   = '2021-10-31';
        $userID = 13;
        $url       = $this->apiPath . '?startdate=' . $startDate . '&enddate=' . $endDate . '&uid=' . $userID;
        $response  = $this->json('GET', $url);

        $response->assertJsonCount(1);
        $response->assertStatus(200);

        $post = $response->json()[0];
        $this->assertEquals($post['user_id'], $userID);
    }

    public function testGetAllPosts_AllPosts_WithPagination(): void
    {
        $limit = 2;
        $response = $this->json('GET', $this->apiPath . '?limit=' . $limit);
        $response->assertStatus(200);
        $response->assertJsonCount(2);

        $posts = $response->json();
        foreach ($posts as $i => $post) {
            $this->assertEquals($i+1, $post['id']);
        }

        $response = $this->json('GET', $this->apiPath . '?limit=' . $limit . '&page=' . 3);
        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $posts = $response->json();
        foreach ($posts as $i => $post) {
            // 2 posts per page and start from page 3, mean id start from 5
            $this->assertEquals($i+5, $post['id']);
        }
    }
}
