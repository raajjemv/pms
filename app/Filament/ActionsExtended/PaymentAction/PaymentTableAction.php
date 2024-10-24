<?php

namespace App\Filament\ActionsExtended\PaymentAction;

use Filament\Forms;
use App\Enums\PaymentType;
use App\Models\BookingTransaction;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Filament\ActionsExtended\PaymentAction\PaymentActionTrait;

class PaymentTableAction extends Action
{
    use PaymentActionTrait;
}
