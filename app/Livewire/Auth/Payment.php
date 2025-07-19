<?php

namespace App\Livewire\Auth;

use App\Models\UserPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.auth2')]
class Payment extends Component
{
    use WithFileUploads;

    #[Validate('required|image')]
    public $proof_of_payment;

    public $registration_number = 'A';

    /**
     * Handle an incoming registration request.
     */
    public function pay(): void
    {
        $this->validate();

        $proof_of_payment = $this->proof_of_payment->store('proof_of_payment');

        $this->registration_number = Str::of(fake()->regexify('[A-Za-z0-9]{8}'))->upper();
        UserPayment::create([
            'user_id' => Auth::user()->id,
            'proof_of_payment' => $proof_of_payment,
            'registration_number' => $this->registration_number
        ]);

        $this->modal('success')->show();
    }

    public function closeModal()
    {
        Auth::logout();
        return redirect()->route('home');
    }
}
