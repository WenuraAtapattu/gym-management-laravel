<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MongoReview;
use App\Models\MongoProduct;
use Illuminate\Support\Facades\Auth;

class ReviewForm extends Component
{
    public $productId;
    public $rating = 5;
    public $comment = '';
    public $guestName = '';
    public $guestEmail = '';

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:10|max:1000',
        'guestName' => 'required_if:user_id,null|string|max:255',
        'guestEmail' => 'required_if:user_id,null|email|max:255',
    ];

    public function mount($productId)
    {
        $this->productId = $productId;
    }

    public function submit()
    {
        $this->validate();

        $review = new MongoReview([
            'reviewable_type' => 'App\\Models\\MongoProduct',
            'reviewable_id' => $this->productId,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => 'pending',
        ]);

        if (auth()->check()) {
            $review->user_id = auth()->id();
        } else {
            $review->guest_name = $this->guestName;
            $review->guest_email = $this->guestEmail;
        }

        $review->save();

        session()->flash('message', 'Review submitted successfully!');
        $this->reset(['rating', 'comment', 'guestName', 'guestEmail']);
    }

    public function render()
    {
        return view('livewire.review-form');
    }
}