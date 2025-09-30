<?php

namespace App\Livewire\Admin\Reviews;

use App\Models\Review;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $activeTab = 'pending';
    public $search = '';
    public $perPage = 10;
    public $selectedReview = null;
    public $showRejectModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'activeTab' => ['except' => 'pending'],
    ];

    public function render()
    {
        $this->authorize('manageReviews', Review::class);

        $reviews = $this->activeTab === 'pending'
            ? $this->getPendingReviews()
            : $this->getApprovedReviews();

        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
        ];

        return view('livewire.admin.reviews.index', [
            'reviews' => $reviews,
            'stats' => $stats,
        ]);
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function approve($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        
        $this->authorize('approve', $review);

        $review->update(['is_approved' => true]);
        
        $this->dispatch('saved');
        session()->flash('message', 'Review approved successfully.');
    }

    public function confirmReject($reviewId)
    {
        $this->selectedReview = Review::findOrFail($reviewId);
        $this->showRejectModal = true;
    }

    public function reject()
    {
        if (!$this->selectedReview) {
            return;
        }

        $this->authorize('reject', $this->selectedReview);

        $this->selectedReview->delete();
        $this->showRejectModal = false;
        $this->selectedReview = null;
        
        $this->dispatch('saved');
        session()->flash('message', 'Review rejected successfully.');
    }

    protected function getPendingReviews()
    {
        return Review::with(['user', 'reviewable'])
            ->where('is_approved', false)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('comment', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->paginate($this->perPage);
    }

    protected function getApprovedReviews()
    {
        return Review::with(['user', 'reviewable'])
            ->where('is_approved', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('comment', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->latest()
            ->paginate($this->perPage);
    }
}
