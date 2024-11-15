<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Full Name -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- DBA -->
            <div>
                <x-input-label for="dba" :value="__('DBA')" />
                <x-text-input id="dba" class="block mt-1 w-full" type="text" name="dba" :value="old('dba')" required />
                <x-input-error :messages="$errors->get('dba')" class="mt-2" />
            </div>
        </div>

        <!-- Primary Contact Phone and Position -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="primary_contact_phone" :value="__('Primary Contact Phone')" />
                <x-text-input id="primary_contact_phone" class="block mt-1 w-full" type="text" name="primary_contact_phone" :value="old('primary_contact_phone')" required />
                <x-input-error :messages="$errors->get('primary_contact_phone')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="primary_contact_position" :value="__('Primary Contact Position')" />
                <x-text-input id="primary_contact_position" class="block mt-1 w-full" type="text" name="primary_contact_position" :value="old('primary_contact_position')" required />
                <x-input-error :messages="$errors->get('primary_contact_position')" class="mt-2" />
            </div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password and Confirm Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Address (Street Number, Street Name, Postal Code) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <div>
                <x-input-label for="address.street_number" :value="__('Street Number')" />
                <x-text-input id="address.street_number" class="block mt-1 w-full" type="text" name="address[street_number]" :value="old('address.street_number')" />
                <x-input-error :messages="$errors->get('address.street_number')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address.street_name" :value="__('Street Name')" />
                <x-text-input id="address.street_name" class="block mt-1 w-full" type="text" name="address[street_name]" :value="old('address.street_name')" />
                <x-input-error :messages="$errors->get('address.street_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address.postal_code" :value="__('Postal Code')" />
                <x-text-input id="address.postal_code" class="block mt-1 w-full" type="text" name="address[postal_code]" :value="old('address.postal_code')" />
                <x-input-error :messages="$errors->get('address.postal_code')" class="mt-2" />
            </div>
        </div>

        <!-- City and Province -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <x-input-label for="address.city" :value="__('City')" />
                <x-text-input id="address.city" class="block mt-1 w-full" type="text" name="address[city]" :value="old('address.city')" required />
                <x-input-error :messages="$errors->get('address.city')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="address.province" :value="__('Province')" />
                <select id="address.province" class="block mt-1 w-full" name="address[province]" required>
                    <option value="">{{ __('Select Province') }}</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" @if(old('address.province') == $province->id) selected @endif>{{ $province->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('address.province')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-center mt-4">
            <x-primary-button>
                {{ __('Register') }}
            </x-primary-button>
        </div>
        
    </form>
</x-guest-layout>
