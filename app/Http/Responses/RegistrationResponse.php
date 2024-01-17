<?php

namespace App\Http\Responses;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class
RegistrationResponse extends \Filament\Http\Responses\Auth\RegistrationResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (Filament::getCurrentPanel()->getId() === 'candidate') {
            return redirect()->route('application-form.success');
        }

        return parent::toResponse($request);
    }
}
