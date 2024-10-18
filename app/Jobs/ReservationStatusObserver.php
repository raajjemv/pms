<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\BookingReservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReservationStatusObserver implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // $this->onQueue('statusObserver');
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
            ->whereDate('to','<=', $today)
            ->where('status',  'check-in')
            ->take(100)
            ->get();


        $filter = $reservations->filter(function ($reservation) use (&$today) {
            $tenant_check_out_time = Carbon::createFromFormat('H:i', $reservation->tenant->check_out_time);
            return $today->gte($tenant_check_out_time);
            // return $today->gte($tenant_check_out_time) && $reservation->to->gte($tenant_check_out_time);
        });

        $filter->each(function ($reservation) {
            $reservation->status = 'overstay';
            $reservation->save();
        });
    }
}
