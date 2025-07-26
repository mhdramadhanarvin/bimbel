<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = 'name';

    public string $email = 'user2@example.com';

    public string $gender = 'male';

    public string $place_of_birth = 'adwmwak';

    public string $date_of_birth = '2001/01/01';

    public string $religion = 'islam';

    public string $address = 'address';

    public string $phone_number = '08';

    public string $origin_school = 'sma';

    public string $programme = 'tni';

    public string $parent_name = 'asno';

    public string $parent_phone_number = '08';

    public string $parent_address = 'jl';

    /* public string $password = ''; */
    /**/
    /* public string $password_confirmation = ''; */

    public function mount() {}

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'gender' => ['required'],
            'place_of_birth' => ['required'],
            'date_of_birth' => ['required'],
            'religion' => ['required'],
            'address' => ['required'],
            'phone_number' => ['required'],
            'origin_school' => ['required'],
            'programme' => ['required'],
            'parent_name' => ['required'],
            'parent_phone_number' => ['required'],
            'parent_address' => ['required'],
            /* 'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()], */
        ]);

        $validated['password'] = Hash::make('password');

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('payment', absolute: false), navigate: true);
    }
}
