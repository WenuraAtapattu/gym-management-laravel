<?php

namespace Tests\Feature;

use App\Models\MongoReview;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Helpers\MongoTestHelper;

/**
 * @group mongodb
 */

class MongoReviewTest extends TestCase
{
    use RefreshDatabase, WithFaker, MongoTestHelper;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Create a test product
        $this->product = Product::factory()->create();
        
        // Set up testing environment
        config([
            'database.default' => 'mongodb',
            'database.connections.mongodb.database' => env('MONGODB_DATABASE', 'gym_management_test'),
            'database.connections.mongodb.database_testing' => env('MONGODB_DATABASE_TEST', 'gym_management_test'),
        ]);
        
        // Clear all collections before each test
        mongodb_clear_collections();
    }

    /** @test */
    public function user_can_create_a_review()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->user);
        
        $response = $this->postJson("/api/mongo/products/{$this->product->id}/reviews", [
            'rating' => 5,
            'title' => 'Great product!',
            'comment' => 'I really enjoyed this product. Highly recommended!',
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'rating',
                    'title',
                    'comment',
                    'is_approved',
                    'created_at',
                    'updated_at',
                ]
            ]);
            
        $this->assertMongoDBHas('reviews', [
            'user_id' => (string) $this->user->id,
            'reviewable_id' => (string) $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 5,
            'title' => 'Great product!',
            'is_approved' => false, // Should be pending approval by default
        ]);
    }

    /** @test */
    public function guest_can_view_approved_reviews()
    {
        // Create an approved review
        $approvedReview = MongoReview::factory()->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'is_approved' => true,
        ]);
        
        // Create a pending review (should not be visible to guests)
        MongoReview::factory()->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'is_approved' => false,
        ]);
        
        $response = $this->getJson("/api/mongo/products/{$this->product->id}/reviews");
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment([
                'id' => $approvedReview->id,
                'rating' => $approvedReview->rating,
            ]);
    }

    /** @test */
    public function user_can_update_own_review()
    {
        $review = MongoReview::factory()->create([
            'user_id' => $this->user->id,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 3,
            'title' => 'Average product',
            'comment' => 'It was okay, I guess.',
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->putJson("/api/mongo/products/{$this->product->id}/reviews/{$review->id}", [
            'rating' => 4,
            'title' => 'Good product',
            'comment' => 'Actually, it\'s better than I initially thought!',
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'rating' => 4,
                    'title' => 'Good product',
                    'comment' => 'Actually, it\'s better than I initially thought!',
                ]
            ]);
    }

    /** @test */
    public function user_cannot_update_another_users_review()
    {
        $otherUser = User::factory()->create();
        $review = MongoReview::factory()->create([
            'user_id' => $otherUser->id,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
        ]);
        
        $this->actingAs($this->user);
        
        $response = $this->putJson("/api/mongo/products/{$this->product->id}/reviews/{$review->id}", [
            'rating' => 1,
            'title' => 'Terrible!',
            'comment' => 'Changed my mind, this is awful!',
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_manage_all_reviews()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $review = MongoReview::factory()->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'is_approved' => false,
        ]);
        
        $this->actingAs($admin);
        
        // Approve the review
        $response = $this->putJson("/api/mongo/products/{$this->product->id}/reviews/{$review->id}", [
            'is_approved' => true,
        ]);
        
        $response->assertStatus(200);
        $this->assertTrue($review->fresh()->is_approved);
        
        // Delete the review
        $response = $this->deleteJson("/api/mongo/products/{$this->product->id}/reviews/{$review->id}");
        $response->assertStatus(200);
        $this->assertNull(MongoReview::find($review->id));
    }

    /** @test */
    public function can_get_review_statistics()
    {
        // Create some test reviews
        MongoReview::factory()->count(3)->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 5,
            'is_approved' => true,
        ]);
        
        MongoReview::factory()->count(2)->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 3,
            'is_approved' => true,
        ]);
        
        // 2 pending reviews
        MongoReview::factory()->count(2)->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 4,
            'is_approved' => false,
        ]);
        
        $response = $this->getJson("/api/mongo/products/{$this->product->id}/reviews/stats");
        
        $response->assertStatus(200)
            ->assertJson([
                'total' => 7, // 5 approved + 2 pending
                'approved' => 5,
                'pending' => 2,
                'average_rating' => 4.2, // (5*3 + 3*2) / 5 = 4.2
            ]);
    }
}
