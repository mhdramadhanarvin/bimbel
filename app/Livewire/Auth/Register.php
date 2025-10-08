<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    /* public string $name = 'test'; */

    public string $name = '';

    public string $email = '';

    /* public string $email = 'test@example.com'; */

    /* public string $gender = 'male'; */

    public string $gender = '';

    /* public string $place_of_birth = 'test'; */

    public string $place_of_birth = '';

    /* public string $date_of_birth = '2000-01-01'; */

    public string $date_of_birth = '';

    /* public string $religion = 'islam'; */

    public string $religion = '';

    /* public string $address = 'test'; */

    public string $address = '';

    /* public string $phone_number = '08'; */

    public string $phone_number = '';

    /* public string $origin_school = 'test'; */

    public string $origin_school = '';

    /* public string $programme = 'polri'; */

    public string $programme = '';

    /* public string $parent_name = 'test'; */

    public string $parent_name = '';

    /* public string $parent_phone_number = '08'; */

    public string $parent_phone_number = '';

    /* public string $parent_address = 'test'; */

    public string $parent_address = '';

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
