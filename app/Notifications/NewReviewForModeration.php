<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewForModeration extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The review instance.
     *
     * @var \App\Models\Review
     */
    public $review;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $reviewable = $this->review->reviewable;
        $reviewableName = $reviewable ? ($reviewable->getAttribute('name') ?? 'Item') : 'Item';
        $reviewerName = $this->review->user ? ($this->review->user->getAttribute('name') ?? $this->review->guest_name) : ($this->review->guest_name ?? 'Guest');
        $rating = $this->review->rating;
        $title = $this->review->title;
        $comment = $this->review->comment;
        $reviewUrl = route('admin.reviews.edit', $this->review);

        return (new MailMessage)
            ->subject("New Review for {$reviewableName} Requires Moderation")
            ->greeting('New Review for Moderation')
            ->line("A new review has been submitted by {$reviewerName} for {$reviewableName}.")
            ->line("Rating: {$rating} stars")
            ->line("Title: {$title}")
            ->line("Comment: {$comment}")
            ->action('Moderate Review', $reviewUrl)
            ->line('Please review this content to ensure it meets our community guidelines.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $reviewable = $this->review->reviewable;
        
        return [
            'review_id' => $this->review->id,
            'reviewable_id' => $this->review->reviewable_id,
            'reviewable_type' => $this->review->reviewable_type,
            'reviewable_name' => $reviewable ? ($reviewable->getAttribute('name') ?? 'Item') : 'Item',
            'user_id' => $this->review->user_id,
            'user_name' => $this->review->user ? ($this->review->user->getAttribute('name') ?? $this->review->guest_name) : ($this->review->guest_name ?? 'Guest'),
            'rating' => $this->review->rating,
            'title' => $this->review->title,
            'message' => 'A new review requires moderation',
            'url' => route('admin.reviews.edit', $this->review),
        ];
    }
}
