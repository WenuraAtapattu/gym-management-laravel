<?php

namespace App\Livewire\Admin\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $showOrderModal = false;
    public $orderId;
    public $userId;
    public $status = 'pending';
    public $totalAmount;
    public $paymentStatus = 'pending';
    public $search = '';
    public $statusFilter = '';
    public $dateFrom;
    public $dateTo;

    protected $rules = [
        'userId' => 'required|exists:users,id',
        'status' => 'required|in:pending,processing,completed,cancelled',
        'totalAmount' => 'required|numeric|min:0',
        'paymentStatus' => 'required|in:pending,paid,failed,refunded',
    ];

    public function render()
    {
        $query = Order::with(['user', 'items.product'])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFrom && $this->dateTo, function ($query) {
                $query->whereBetween('created_at', [
                    $this->dateFrom . ' 00:00:00',
                    $this->dateTo . ' 23:59:59'
                ]);
            });

        $orders = $query->latest()->paginate(10);
        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        $members = User::where('is_admin', false)->get();

        return view('livewire.order-manager', [
            'orders' => $orders,
            'statuses' => $statuses,
            'paymentStatuses' => $paymentStatuses,
            'members' => $members,
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showOrderModal = true;
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $this->orderId = $order->id;
        $this->userId = $order->user_id;
        $this->status = $order->status;
        $this->totalAmount = $order->total_amount;
        $this->paymentStatus = $order->payment_status;
        $this->showOrderModal = true;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $orderData = [
                'user_id' => $this->userId,
                'status' => $this->status,
                'total_amount' => $this->totalAmount,
                'payment_status' => $this->paymentStatus,
            ];

            if ($this->orderId) {
                $order = Order::find($this->orderId);
                $order->update($orderData);
                $message = 'Order updated successfully';
            } else {
                $order = Order::create($orderData);
                $message = 'Order created successfully';
            }

            DB::commit();
            $this->showOrderModal = false;
            session()->flash('message', $message);
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error saving order: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $order = Order::find($id);
        if ($order) {
            $order->delete();
            session()->flash('message', 'Order deleted successfully');
        }
    }

    public function updateStatus($id, $status)
    {
        $order = Order::find($id);
        if ($order) {
            $order->update(['status' => $status]);
            session()->flash('message', 'Order status updated successfully');
        }
    }

    private function resetForm()
    {
        $this->reset([
            'orderId', 'userId', 'status', 'totalAmount', 'paymentStatus',
        ]);
    }
}
