<?php

namespace App\Console\Commands;

use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ManageReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reviews:manage 
                            {action : The action to perform (list, show, approve, reject, delete, stats)}
                            {review_id? : The ID of the review to act on}
                            {--user= : Filter by user ID or email}
                            {--product= : Filter by product ID}
                            {--status=pending : Filter by status (pending, approved, rejected)}
                            {--min-rating= : Filter by minimum rating}
                            {--max-rating= : Filter by maximum rating}
                            {--limit=20 : Number of results to show}
                            {--force : Skip confirmation prompt} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage product reviews from the command line';

    /**
     * The review service instance.
     *
     * @var \App\Services\ReviewService
     */
    protected $reviewService;

    /**
     * Create a new command instance.
     *
     * @param  \App\Services\ReviewService  $reviewService
     * @return void
     */
    public function __construct(ReviewService $reviewService)
    {
        parent::__construct();
        $this->reviewService = $reviewService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $reviewId = $this->argument('review_id');

        if (!method_exists($this, $action)) {
            $this->error("Action '{$action}' not recognized.");
            $this->line('Available actions: list, show, approve, reject, delete, stats');
            return 1;
        }

        return $this->$action($reviewId);
    }

    /**
     * List reviews with optional filters.
     */
    protected function list()
    {
        $query = Review::query()
            ->with(['user', 'reviewable'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->option('user')) {
            $user = User::where('id', $this->option('user'))
                ->orWhere('email', $this->option('user'))
                ->first();

            if ($user) {
                $query->where('user_id', $user->id);
            } else {
                $this->warn("No user found with ID or email: " . $this->option('user'));
                return 1;
            }
        }

        if ($this->option('product')) {
            $query->where('reviewable_type', 'App\\Models\\Product')
                ->where('reviewable_id', $this->option('product'));
        }

        if ($this->option('status') !== 'all') {
            $query->where('is_approved', $this->option('status') === 'approved');
        }

        if ($this->option('min-rating')) {
            $query->where('rating', '>=', $this->option('min-rating'));
        }

        if ($this->option('max-rating')) {
            $query->where('rating', '<=', $this->option('max-rating'));
        }

        $reviews = $query->limit($this->option('limit'))->get();

        if ($reviews->isEmpty()) {
            $this->info('No reviews found matching the criteria.');
            return 0;
        }

        $this->table(
            ['ID', 'Product', 'Rating', 'Title', 'User', 'Status', 'Date'],
            $reviews->map(function ($review) {
                return [
                    $review->id,
                    $review->reviewable->name ?? 'N/A',
                    str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating),
                    $review->title,
                    $review->user->name,
                    $review->is_approved ? 'Approved' : 'Pending',
                    $review->created_at->format('Y-m-d H:i'),
                ];
            })
        );

        return 0;
    }

    /**
     * Show details of a specific review.
     */
    protected function show($reviewId)
    {
        $review = Review::with(['user', 'reviewable', 'reports'])->find($reviewId);

        if (!$review) {
            $this->error("Review #{$reviewId} not found.");
            return 1;
        }

        $this->info("Review #{$review->id}");
        $this->line(str_repeat('-', 50));
        $this->line("Product: " . (isset($review->reviewable->name) ? $review->reviewable->name : 'N/A'));
        $this->line("Rating:  " . str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating));
        $this->line("Title:   {$review->title}");
        $this->line("User:    {$review->user->name} ({$review->user->email})");
        $this->line("Status:  " . ($review->is_approved ? 'Approved' : 'Pending'));
        $this->line("Date:    {$review->created_at->format('F j, Y H:i')}");
        $this->line("\n{$review->comment}");

        if ($review->reports->isNotEmpty()) {
            $this->line("\nReports: " . $review->reports->count());
            foreach ($review->reports as $report) {
                $this->line("- {$report->reason} (by {$report->reporter->name} on {$report->created_at->format('Y-m-d')})");
            }
        }

        return 0;
    }

    /**
     * Approve a review.
     */
    protected function approve($reviewId)
    {
        $review = Review::find($reviewId);

        if (!$review) {
            $this->error("Review #{$reviewId} not found.");
            return 1;
        }

        if ($review->is_approved) {
            $this->info("Review #{$reviewId} is already approved.");
            return 0;
        }

        if (!$this->option('force') && !$this->confirm("Approve review #{$reviewId}?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->reviewService->approveReview($review, 'Approved via command line');
        $this->info("Review #{$reviewId} has been approved.");

        return 0;
    }

    /**
     * Reject a review.
     */
    protected function reject($reviewId)
    {
        $review = Review::find($reviewId);

        if (!$review) {
            $this->error("Review #{$reviewId} not found.");
            return 1;
        }

        if (!$review->is_approved) {
            $this->info("Review #{$reviewId} is already in a pending or rejected state.");
            return 0;
        }

        $reason = $this->option('reason') ?: $this->ask('Please provide a reason for rejection');

        if (empty($reason)) {
            $this->error('A reason is required to reject a review.');
            return 1;
        }

        if (!$this->option('force') && !$this->confirm("Reject review #{$reviewId}?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->reviewService->rejectReview($review, $reason);
        $this->info("Review #{$reviewId} has been rejected.");

        return 0;
    }

    /**
     * Delete a review.
     */
    protected function delete($reviewId)
    {
        $review = Review::find($reviewId);

        if (!$review) {
            $this->error("Review #{$reviewId} not found.");
            return 1;
        }

        if (!$this->option('force') && !$this->confirm("Are you sure you want to delete review #{$reviewId}? This action cannot be undone.")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->reviewService->deleteReview($review);
        $this->info("Review #{$reviewId} has been deleted.");

        return 0;
    }

    /**
     * Show review statistics.
     */
    protected function stats()
    {
        $stats = [
            'Total Reviews' => Review::count(),
            'Approved' => Review::where('is_approved', true)->count(),
            'Pending' => Review::where('is_approved', false)->count(),
            'Average Rating' => number_format(Review::where('is_approved', true)->avg('rating') ?? 0, 1),
            'Reports' => DB::table('reports')->count(),
        ];

        $this->info('Review Statistics');
        $this->line(str_repeat('-', 30));
        
        foreach ($stats as $label => $value) {
            $this->line(sprintf("%-15s: %s", $label, $value));
        }

        // Rating distribution
        $distribution = Review::select(
            'rating',
            DB::raw('COUNT(*) as count'),
            DB::raw('ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reviews WHERE is_approved = 1), 1) as percentage')
        )
        ->where('is_approved', true)
        ->groupBy('rating')
        ->orderBy('rating', 'desc')
        ->get();

        $this->line("\nRating Distribution:");
        
        $rows = [];
        foreach ($distribution as $row) {
            $bar = str_repeat('■', (int) ($row->percentage / 2));
            $rows[] = [
                str_repeat('★', $row->rating) . str_repeat('☆', 5 - $row->rating),
                $row->count,
                $row->percentage . '%',
                $bar,
            ];
        }

        $this->table(['Rating', 'Count', 'Percentage', 'Chart'], $rows);

        return 0;
    }
}
