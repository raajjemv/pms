<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use App\Filament\Pages\SchedulerPage;
use Illuminate\Http\RedirectResponse;
use App\Filament\Resources\OrderResource;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return redirect()->to(SchedulerPage::getUrl(['tenant' => auth()->user()->current_tenant_id]));
        }
    }
}
