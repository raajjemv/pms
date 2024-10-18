@props(['reservation','booking_number'])
<div class="flex items-center space-x-3">
    <a wire:navigate href="{{ App\Filament\Pages\SchedulerPage::getUrl() }}">
        <i class="fa-solid fa-arrow-left-long"></i>
    </a>
    <span>{{ $reservation->booking_customer }}</span>
    <span class="text-lg font-normal text-gray-500">{{ $booking_number }}</span>
</div>
