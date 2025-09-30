<?php

namespace App\Livewire\Admin\Payments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\User;
use App\Models\Membership;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;
    
    public $showModal = false;
    public $paymentId;
    public $memberId;
    public $membershipId;
    public $amount;
    public $paymentDate;
    public $paymentMethod = 'cash';
    public $status = 'completed';
    public $notes;
    
    public $search = '';
    public $dateFrom;
    public $dateTo;
    public $statusFilter = '';
    public $paymentMethodFilter = '';
    
    protected function rules()
    {
        return [
            'memberId' => 'required|exists:users,id',
            'membershipId' => 'required|exists:memberships,id',
            'amount' => 'required|numeric|min:0',
            'paymentDate' => 'required|date',
            'paymentMethod' => 'required|in:cash,credit_card,debit_card,bank_transfer,other',
            'status' => 'required|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function mount()
    {
        $this->paymentDate = now()->format('Y-m-d');
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = Payment::with(['member', 'membership'])
            ->when($this->search, function($query) {
                $query->whereHas('member', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFrom && $this->dateTo, function($query) {
                $query->whereBetween('payment_date', [
                    $this->dateFrom, 
                    $this->dateTo
                ]);
            })
            ->when($this->statusFilter, function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->paymentMethodFilter, function($query) {
                $query->where('payment_method', $this->paymentMethodFilter);
            })
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc');

        return view('livewire.payment-manager', [
            'payments' => $query->paginate(15),
            'members' => User::where('is_admin', false)->orderBy('name')->get(),
            'memberships' => Membership::with('user')->get(),
            'paymentMethods' => [
                'cash' => 'Cash',
                'credit_card' => 'Credit Card',
                'debit_card' => 'Debit Card',
                'bank_transfer' => 'Bank Transfer',
                'other' => 'Other'
            ],
            'paymentStatuses' => [
                'pending' => 'Pending',
                'completed' => 'Completed',
                'failed' => 'Failed',
                'refunded' => 'Refunded'
            ]
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $payment = Payment::findOrFail($id);
        $this->paymentId = $id;
        $this->memberId = $payment->user_id;
        $this->membershipId = $payment->membership_id;
        $this->amount = $payment->amount;
        $this->paymentDate = $payment->payment_date->format('Y-m-d');
        $this->paymentMethod = $payment->payment_method;
        $this->status = $payment->status;
        $this->notes = $payment->notes;
        $this->showModal = true;
    }

    public function store()
    {
        $validatedData = $this->validate();
        
        $paymentData = [
            'user_id' => $this->memberId,
            'membership_id' => $this->membershipId,
            'amount' => $this->amount,
            'payment_date' => $this->paymentDate,
            'payment_method' => $this->paymentMethod,
            'status' => $this->status,
            'notes' => $this->notes,
        ];

        if ($this->paymentId) {
            // Update existing payment
            Payment::find($this->paymentId)->update($paymentData);
            session()->flash('message', 'Payment updated successfully.');
        } else {
            // Create new payment
            $payment = Payment::create($paymentData);
            
            // If payment is completed, update membership end date
            if ($this->status === 'completed') {
                $this->updateMembershipEndDate($payment);
            }
            
            session()->flash('message', 'Payment recorded successfully.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    private function updateMembershipEndDate($payment)
    {
        $membership = Membership::find($this->membershipId);
        if (!$membership) return;
        
        $startDate = $membership->end_date > now() 
            ? $membership->end_date 
            : now();
            
        $endDate = (clone $startDate)->addMonth();
        
        $membership->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'payment_status' => 'paid'
        ]);
    }

    public function delete($id)
    {
        Payment::find($id)->delete();
        session()->flash('message', 'Payment record deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetInputFields()
    {
        $this->paymentId = null;
        $this->memberId = null;
        $this->membershipId = null;
        $this->amount = '';
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentMethod = 'cash';
        $this->status = 'completed';
        $this->notes = '';
    }

    public function updatedMemberId($value)
    {
        if ($value) {
            $membership = Membership::where('user_id', $value)
                ->latest('end_date')
                ->first();
                
            if ($membership) {
                $this->membershipId = $membership->id;
                $this->amount = $membership->price;
            }
        }
    }
}
