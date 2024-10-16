<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\BookingReservation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReservationOverStayChargableObserver implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = now();

        $reservations = BookingReservation::query()
            ->with('tenant')
            ->withoutGlobalScopes()
            ->whereDate('to', $today)
            ->where('status',  'overstay')
            ->take(100)
            ->get();

        $filter = $reservations->filter(function ($reservation) use (&$today) {
            $tenant_late_check_out_time = Carbon::createFromFormat('H:i', $reservation->tenant->late_check_out_time);
            return $today->gte($tenant_late_check_out_time);
        });

        $filter->each(function ($reservation) {
            // $reservation->status = 'overstay';
            // $reservation->save();
        });
    }
}
