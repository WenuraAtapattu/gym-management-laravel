<?php

namespace App\Livewire\Admin\Members;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showModal = false;
    public $memberId;
    public $membershipId;
    public $name, $email, $phone, $address, $gender, $emergency_contact;
    public $membership_type, $start_date, $end_date, $status = 'active';
    public $price, $payment_status;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($this->memberId ? "," . $this->memberId : ''),
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'emergency_contact' => 'required|string|max:255',
            'membership_type' => 'required|in:basic,premium,vip,student,senior,family',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,expired,cancelled,on_hold',
            'price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:paid,pending,overdue,refunded'
        ];
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field 
            ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
            : 'asc';

        $this->sortField = $field;
    }

    public function render()
    {
        $members = User::where('is_admin', false)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->with(['memberships' => function($query) {
                $query->latest()->first();
            }])
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.member-management', [
            'members' => $members
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $member = User::with(['memberships' => function($query) {
            $query->latest()->first();
        }])->findOrFail($id);

        $this->memberId = $id;
        $this->name = $member->name;
        $this->email = $member->email;
        $this->phone = $member->phone;
        $this->address = $member->address;
        $this->gender = $member->gender;
        $this->emergency_contact = $member->emergency_contact;
        
        if ($member->memberships->isNotEmpty()) {
            $this->membershipId = $member->memberships[0]->id;
            $this->membership_type = $member->memberships[0]->type;
            $this->start_date = $member->memberships[0]->start_date;
            $this->end_date = $member->memberships[0]->end_date;
            $this->status = $member->memberships[0]->status;
            $this->price = $member->memberships[0]->price;
            $this->payment_status = $member->memberships[0]->payment_status;
        }

        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'gender' => $this->gender,
                'emergency_contact' => $this->emergency_contact,
            ];

            // Update or create user
            $user = User::updateOrCreate(
                ['id' => $this->memberId],
                array_merge($userData, [
                    'password' => $this->memberId ? null : bcrypt('password') // Only set password for new users
                ])
            );

            // Create or update membership
            $membershipData = [
                'type' => $this->membership_type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'status' => $this->status,
                'price' => $this->price,
                'payment_status' => $this->payment_status,
            ];

            if ($this->membershipId) {
                $user->memberships()->where('id', $this->membershipId)->update($membershipData);
            } else {
                $user->memberships()->create($membershipData);
            }
        });

        session()->flash('message', 
            $this->memberId ? 'Member updated successfully.' : 'Member created successfully.'
        );

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'Member deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetInputFields()
    {
        $this->memberId = null;
        $this->membershipId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->gender = '';
        $this->emergency_contact = '';
        $this->membership_type = '';
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addMonth()->format('Y-m-d');
        $this->status = 'active';
        $this->price = '';
        $this->payment_status = '';
    }
}
