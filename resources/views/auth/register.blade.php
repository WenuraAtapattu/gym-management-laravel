<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-50 to-gray-100">
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-2xl">
            <!-- Gym Logo -->
            <div class="flex flex-col items-center mb-8">
                <a href="/" class="mb-4">
                    <div class="flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Gym Logo" class="h-16 w-auto">
                    </div>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Create Your Account</h2>
                <p class="text-sm text-gray-600 mt-1">Join our fitness community today</p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="space-y-1">
                    <x-label for="name" value="{{ __('Full Name') }}" class="text-sm font-medium text-gray-700" />
                    <x-input id="name" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                            type="text" name="name" :value="old('name')" required autofocus autocomplete="name" 
                            placeholder="John Doe" />
                </div>

                <div class="space-y-1">
                    <x-label for="email" value="{{ __('Email') }}" class="text-sm font-medium text-gray-700" />
                    <x-input id="email" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                            type="email" name="email" :value="old('email')" required autocomplete="username" 
                            placeholder="your@email.com" />
                </div>

                <div class="space-y-1">
                    <x-label for="password" value="{{ __('Password') }}" class="text-sm font-medium text-gray-700" />
                    <x-input id="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                            type="password" name="password" required autocomplete="new-password" 
                            placeholder="••••••••" />
                </div>

                <div class="space-y-1">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-sm font-medium text-gray-700" />
                    <x-input id="password_confirmation" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                            type="password" name="password_confirmation" required autocomplete="new-password" 
                            placeholder="••••••••" />
                </div>

                <div class="space-y-1">
                    <x-label for="phone" value="{{ __('Phone Number') }}" class="text-sm font-medium text-gray-700" />
                    <x-input id="phone" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                            type="tel" name="phone" :value="old('phone')" autocomplete="tel" 
                            placeholder="+1 (555) 123-4567" />
                </div>

                <div class="space-y-1">
                    <x-label for="street" value="{{ __('Street Address') }}" class="text-sm font-medium text-gray-700" />
                    <x-input id="street" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                            type="text" name="street" :value="old('street')" autocomplete="street-address" 
                            placeholder="123 Fitness St" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <x-label for="city" value="{{ __('City') }}" class="text-sm font-medium text-gray-700" />
                        <x-input id="city" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                                type="text" name="city" :value="old('city')" autocomplete="address-level2" 
                                placeholder="New York" />
                    </div>

                    <div class="space-y-1">
                        <x-label for="state" value="{{ __('State/Province') }}" class="text-sm font-medium text-gray-700" />
                        <x-input id="state" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                                type="text" name="state" :value="old('state')" autocomplete="address-level1" 
                                placeholder="NY" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <x-label for="postal_code" value="{{ __('Postal Code') }}" class="text-sm font-medium text-gray-700" />
                        <x-input id="postal_code" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" 
                                type="text" name="postal_code" :value="old('postal_code')" autocomplete="postal-code" 
                                placeholder="10001" />
                    </div>

                    <div class="space-y-1">
                        <x-label for="country" value="{{ __('Country') }}" class="text-sm font-medium text-gray-700" />
                        <select id="country" name="country" autocomplete="country" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            <option value="">Select a country</option>
                            <option value="US" {{ old('country') == 'US' ? 'selected' : '' }}>United States</option>
                            <option value="CA" {{ old('country') == 'CA' ? 'selected' : '' }}>Canada</option>
                            <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                            <option value="AU" {{ old('country') == 'AU' ? 'selected' : '' }}>Australia</option>
                            <option value="JP" {{ old('country') == 'JP' ? 'selected' : '' }}>Japan</option>
                            <option value="IN" {{ old('country') == 'IN' ? 'selected' : '' }}>India</option>
                            <option value="CN" {{ old('country') == 'CN' ? 'selected' : '' }}>China</option>
                            <option value="BR" {{ old('country') == 'BR' ? 'selected' : '' }}>Brazil</option>
                            <option value="DE" {{ old('country') == 'DE' ? 'selected' : '' }}>Germany</option>
                            <option value="FR" {{ old('country') == 'FR' ? 'selected' : '' }}>France</option>
                        </select>
                    </div>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-3">
                        <label class="flex items-start">
                            <x-checkbox name="terms" id="terms" required class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                            <span class="ml-2 text-xs text-gray-600">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-indigo-600 hover:text-indigo-800 font-medium">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-indigo-600 hover:text-indigo-800 font-medium">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    </div>
                @endif

                <div class="mt-6">
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        {{ __('Create Account') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        {{ __('Sign in') }}
                    </a>
                </p>
                <div class="mt-2">
                    <a href="{{ route('admin.login') }}" class="text-xs text-gray-500 hover:text-gray-700">
                        {{ __('Administrator Login') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
