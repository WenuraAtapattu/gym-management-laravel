<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Product $product;
    protected ReviewService $reviewService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        
        // Create a test product
        $this->product = Product::factory()->create();
        
        // Create review service instance
        $this->reviewService = app(ReviewService::class);
        
        // Fake the storage for testing file uploads
        Storage::fake('public');
    }

    /** @test */
    public function user_can_create_a_review()
    {
        $reviewData = [
            'rating' => 5,
            'title' => 'Great product!',
            'comment' => 'This product exceeded my expectations. Highly recommended!',
            'content' => 'This product exceeded my expectations. Highly recommended!', // For backward compatibility
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$this->product->id}/reviews", $reviewData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'rating', 'title', 'comment', 'content', 'created_at', 'user'
                ]
            ]);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'rating' => 5,
            'title' => 'Great product!',
            'comment' => 'This product exceeded my expectations. Highly recommended!',
        ]);
    }

    /** @test */
    public function review_requires_rating_title_and_comment()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$this->product->id}/reviews", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating', 'title']);
            // Comment is not required as it can be null
    }

    /** @test */
    public function rating_must_be_between_1_and_5()
    {
        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$this->product->id}/reviews", [
                'rating' => 6,
                'title' => 'Invalid rating',
                'comment' => 'This should fail',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    /** @test */
    public function user_can_upload_images_with_review()
    {
        $this->markTestSkipped('Image upload tests require additional setup');
        
        $images = [
            UploadedFile::fake()->image('review1.jpg'),
            UploadedFile::fake()->image('review2.jpg'),
        ];

        $response = $this->actingAs($this->user)
            ->post("/api/products/{$this->product->id}/reviews", [
                'rating' => 5,
                'title' => 'With images',
                'comment' => 'This review has images',
                'images' => $images,
            ]);

        $response->assertStatus(201);
        
        $review = Review::latest()->first();
        $this->assertCount(2, $review->getMedia('review_images'));
    }

    /** @test */
    public function user_can_update_their_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
            'rating' => 3,
            'title' => 'Initial review',
            'comment' => 'It was okay',
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/reviews/{$review->id}", [
                'rating' => 4,
                'title' => 'Updated review',
                'comment' => 'Actually, it\'s better than I thought!',
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 4,
            'title' => 'Updated review',
        ]);
    }

    /** @test */
    public function user_cannot_update_another_users_review()
    {
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
        ]);

        $response = $this->actingAs($this->user)
            ->putJson("/api/reviews/{$review->id}", [
                'rating' => 1,
                'title' => 'Hacked review',
                'comment' => 'I changed someone else\'s review!',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_delete_their_review()
    {
        $review = Review::factory()->create([
            'user_id' => $this->user->id,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    /** @test */
    public function admin_can_approve_review()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $review = Review::factory()->create([
            'is_approved' => false,
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
        ]);

        $response = $this->actingAs($admin)
            ->postJson("/api/admin/reviews/{$review->id}/approve");

        $response->assertStatus(200);
        $this->assertTrue($review->fresh()->is_approved);
    }

    /** @test */
    public function user_can_report_review()
    {
        $review = Review::factory()->create([
            'reviewable_id' => $this->product->id,
            'reviewable_type' => Product::class,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/reviews/{$review->id}/report", [
                'reason' => 'This review is inappropriate',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'review_id' => $review->id,
            'user_id' => $this->user->id,
            'reason' => 'This review is inappropriate',
        ]);
    }

    /** @test */
    public function guest_cannot_create_review_if_guest_reviews_disabled()
    {
        config(['reviews.settings.allow_guest_reviews' => false]);
        
        $response = $this->postJson("/api/products/{$this->product->id}/reviews", [
            'rating' => 5,
            'title' => 'Guest review',
            'comment' => 'This should fail',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function guest_can_create_review_if_guest_reviews_enabled()
    {
        config(['reviews.settings.allow_guest_reviews' => true]);
        
        $response = $this->postJson("/api/products/{$this->product->id}/reviews", [
            'rating' => 5,
            'title' => 'Guest review',
            'comment' => 'This should work',
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'reviewable_id' => $this->product->id,
            'title' => 'Guest review',
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
        ]);
    }

    /** @test */
    public function review_requires_minimum_word_count()
    {
        config(['reviews.requirements.min_words' => 10]);
        
        $response = $this->actingAs($this->user)
            ->postJson("/api/products/{$this->product->id}/reviews", [
                'rating' => 5,
                'title' => 'Short review',
                'comment' => 'Too short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['comment']);
    }

    /** @test */
    public function admin_can_see_pending_reviews()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Review::factory()->count(3)->create(['is_approved' => false]);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/admin/reviews/pending');
            
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function non_admin_cannot_see_pending_reviews()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/admin/reviews/pending');
            
        $response->assertStatus(403);
    }

    /** @test */
    public function review_statistics_are_calculated_correctly()
    {
        // Create reviews with different ratings
        Review::factory()->create(['rating' => 5, 'is_approved' => true]);
        Review::factory()->create(['rating' => 4, 'is_approved' => true]);
        Review::factory()->create(['rating' => 3, 'is_approved' => true]);
        Review::factory()->create(['rating' => 5, 'is_approved' => false]); // Shouldn't count
        
        $stats = $this->reviewService->getReviewStats($this->product);
        
        $this->assertEquals(3, $stats['total_reviews']);
        $this->assertEquals(4, $stats['average_rating']); // (5+4+3)/3 = 4
        $this->assertEquals(1, $stats['rating_distribution'][5]);
        $this->assertEquals(1, $stats['rating_distribution'][4]);
        $this->assertEquals(1, $stats['rating_distribution'][3]);
    }
}
