<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Review $review The review being updated
     * @param string $status The status of the review ('approved', 'rejected', 'reported', 'responded')
     * @param string|null $reason Optional reason for the status change
     */
    public function __construct(
        public Review $review,
        public string $status,
        public ?string $reason = null
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $product = $this->review->product;
        $productName = $product ? $product->getAttribute('name') : 'the product';
        
        $subject = '';
        $greeting = '';
        $line = '';

        switch ($this->status) {
            case 'approved':
                $subject = "Your review for {$productName} has been approved";
                $greeting = 'Review Approved!';
                $line = 'Thank you for your review. It has been published and is now visible to other customers.';
                break;
            
            case 'rejected':
                $subject = "Your review for {$productName} was not approved";
                $greeting = 'Review Not Approved';
                $line = 'Your review did not meet our community guidelines.';
                if ($this->reason) {
                    $line .= ' Reason: ' . $this->reason;
                }
                break;

            case 'reported':
                $subject = "Your review for {$productName} has been reported";
                $greeting = 'Review Reported';
                $line = 'Your review has been reported by other users and is under review by our team.';
                if ($this->reason) {
                    $line .= ' Reason: ' . $this->reason;
                }
                break;

            case 'responded':
                $subject = "New response to your review of {$productName}";
                $greeting = 'New Response to Your Review';
                $line = 'The seller has responded to your review.';
                if ($this->reason) {
                    $line .= ' Response: ' . $this->reason;
                }
                break;
        }

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($line);

        if ($product) {
            $mailMessage->action('View Product', route('products.show', $product));
        }

        return $mailMessage->line('Thank you for being a valued customer!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $message = '';
        $reviewable = $this->review->reviewable;
        
        $data = [
            'reviewable_id' => $this->review->reviewable_id,
            'reviewable_type' => $this->review->reviewable_type,
            'reviewable_name' => $reviewable ? ($reviewable->getAttribute('name') ?? 'Item') : 'Item',
            'status' => $this->status,
        ];

        // Add URL if reviewable has getUrl method
        if ($reviewable && method_exists($reviewable, 'getUrl')) {
            $data['reviewable_url'] = $reviewable->getUrl();
        } else {
            $data['reviewable_url'] = '#';
        }

        switch ($this->status) {
            case 'approved':
                $message = 'Your review has been approved and published.';
                break;
                
            case 'rejected':
                $message = 'Your review has been rejected.';
                if ($this->reason) {
                    $message .= ' Reason: ' . $this->reason;
                }
                break;
                
            case 'reported':
                $message = 'Your review has been reported and is under review.';
                if ($this->reason) {
                    $message .= ' Reason: ' . $this->reason;
                }
                break;
                
            case 'responded':
                $message = 'The seller has responded to your review.';
                if ($this->reason) {
                    $message .= ' Response: ' . $this->reason;
                }
                break;
        }

        return [
            'message' => $message,
            'data' => $data,
        ];
    }
}
