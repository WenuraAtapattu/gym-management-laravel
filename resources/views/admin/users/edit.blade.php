@extends('layouts.original')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800">
            Back to Users
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline"> There were some problems with your input.</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6 space-y-6">
                <!-- Profile Photo -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Profile Photo
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100">
                                <img class="h-full w-full object-cover" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Full Name
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                            class="max-w-lg block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="username" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Username
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                @
                            </span>
                            <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" 
                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 @error('username') border-red-500 @enderror">
                        </div>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Email
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                            class="max-w-lg block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="phone" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Phone
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                            class="max-w-lg block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Role -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Role
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <input id="is_admin" name="is_admin" type="checkbox" value="1" 
                                {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_admin" class="ml-2 block text-sm text-gray-700">
                                Administrator
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Administrators have full access to the admin panel.</p>
                    </div>
                </div>

                <!-- Password Reset -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Password
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="space-y-4">
                            <div>
                                <button type="button" id="change-password-btn" 
                                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Change Password
                                </button>
                                <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password.</p>
                            </div>
                            
                            <div id="password-fields" class="hidden space-y-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password" 
                                        class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                        class="mt-1 block w-full shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Account Status
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" 
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                Active
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Inactive users cannot log in to their accounts.</p>
                    </div>
                </div>

                <!-- Email Verification -->
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">
                        Email Verification
                    </label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <div class="flex items-center">
                            @if($user->hasVerifiedEmail())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Verified
                                </span>
                                <span class="ml-2 text-sm text-gray-500">
                                    {{ $user->email_verified_at->format('M j, Y \a\t g:i A') }}
                                </span>
                                @if(!$user->is($user))
                                    <button type="button" 
                                        onclick="if(confirm('Are you sure you want to unverify this email?')) document.getElementById('unverify-email').submit();"
                                        class="ml-4 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Unverify
                                    </button>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Unverified
                                </span>
                                @if(!$user->is($user))
                                    <button type="button" 
                                        onclick="if(confirm('Are you sure you want to verify this email?')) document.getElementById('verify-email').submit();"
                                        class="ml-4 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Verify Email
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Changes
                </button>
            </div>
        </form>

        <!-- Email Verification Forms -->
        @if(!$user->is($user))
            @if($user->hasVerifiedEmail())
                <form id="unverify-email" action="{{ route('admin.users.email.unverify', $user) }}" method="POST" class="hidden">
                    @csrf
                    @method('PUT')
                </form>
            @else
                <form id="verify-email" action="{{ route('admin.users.email.verify', $user) }}" method="POST" class="hidden">
                    @csrf
                    @method('PUT')
                </form>
            @endif
        @endif
    </div>
</div>

@push('scripts')
<script>
    // Toggle password fields
    document.getElementById('change-password-btn').addEventListener('click', function() {
        const passwordFields = document.getElementById('password-fields');
        const isHidden = passwordFields.classList.contains('hidden');
        
        if (isHidden) {
            passwordFields.classList.remove('hidden');
            this.textContent = 'Cancel';
        } else {
            passwordFields.classList.add('hidden');
            this.textContent = 'Change Password';
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
        }
    });

    // If there are password errors, show the password fields
    @if($errors->has('password'))
        document.getElementById('change-password-btn').click();
    @endif
</script>
@endpush
@endsection
