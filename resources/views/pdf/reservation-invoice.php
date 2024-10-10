<x-layouts.pdf>
    <div class="p-2">
        <div class="text-sm border border-gray-400 ">
            @php
                $tenant = tenant();
            @endphp
            <header class="py-3 space-y-2 text-center border-b border-gray-400 roboto">
                <div class="text-xl">{{ $tenant->name }}</div>
                <div class="">{{ $tenant->address }}</div>
                <div>Phone: {{ $tenant->phone_number }} | Email: {{ $tenant->email }}</div>
                <div>Website: {{ $tenant->website }}</div>
                <div class="text-2xl font-bold pt-7">Tax Invoice</div>
            </header>
            <div class="flex items-start justify-between p-3">
                <table class="text-left">
                    <tr>
                        <th class="py-1">Invoice No </th>
                        <td class="py-1 pl-10 uppercase">{{ str()->random(10) }}</td>
                    </tr>
                    <tr>
                        <th class="py-1">Folio/Res No </th>
                        <td class="py-1 pl-10">{{ $booking->booking_number }}</td>
                    </tr>
                    <tr>
                        <th class="py-1">Guest Name </th>
                        <td class="py-1 pl-10">{{ $reservation->booking_customer }}</td>
                    </tr>
                </table>
                <table class="text-left">
                    <tr>
                        <th class="py-1">Date </th>
                        <td class="py-1 pl-10">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th class="py-1">Booking Source</th>
                        <td class="py-1 pl-10 capitalize">{{ $booking->booking_type }}</td>
                    </tr>
                </table>
            </div>
            <div class="grid grid-cols-4 border-t border-gray-400 divide-x divide-gray-400 ">
                <div class="p-3">
                    <div class="py-1 font-bold">Nationality</div>
                    <div>-</div>
                </div>
                <div class="p-3">
                    <div class="py-1 font-bold">No of Pax</div>
                    <div>{{ $reservation->totalPax() }}</div>
                </div>
                <div class="p-3">
                    <div class="py-1 font-bold">Adult/Children</div>
                    <div>{{ $reservation->adults . ' / ' . $reservation->children }}</div>
                </div>
                <div class="p-3">
                    <div class="py-1 font-bold">Room</div>
                    <div>{{ $reservation->room->roomType->name . ' - ' . $reservation->room->room_number }}</div>
                </div>
            </div>
            <div class="grid grid-cols-4 border-t border-gray-400 divide-x divide-gray-400 ">
                <div class="p-3">
                    <div class="py-1 font-bold">Arrival Date</div>
                    <div>{{ $reservation->from->format('d/m/Y') }} - {{ $reservation->check_in }}</div>
                </div>
                <div class="p-3">
                    <div class="py-1 font-bold">Departure Date</div>
                    <div>{{ $reservation->to->format('d/m/Y') }} - {{ $reservation->check_out }}</div>
                </div>
                <div class="p-3">
                    <div class="py-1 font-bold">Reservation Date</div>
                    <div>{{ $reservation->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="grid grid-cols-2 divide-x divide-gray-400">
                    <div class="p-3">
                        <div class="py-1 font-bold">Rate Plan</div>
                        <div>{{ $reservation->ratePlan->code }}</div>
                    </div>
                    <div class="p-3">
                        <div class="py-1 font-bold">Tariff</div>
                        <div>{{ $reservation->averageRate() }}</div>
                    </div>
                </div>
            </div>
            <div class="p-3 border-t border-gray-400">
                <table class="w-full text-left ">
                    <thead>
                        <tr>
                            <th class="py-1">Date</th>
                            <th class="py-1">Description</th>
                            <th class="py-1 text-right">Rate</th>
                            <th class="py-1 text-right">Charge</th>
                        </tr>
                    </thead>
                    @foreach ($reservation->bookingTransactions->whereNotIn('transaction_type', App\Enums\PaymentType::getAllValues())->sortBy('date') as $bookingTransaction)
                        @php
                            $service_charge = ($bookingTransaction->rate * 10) / 100;
                            $tgst = (($bookingTransaction->rate + $service_charge) * 16) / 100;
                            $green_tax = $reservation->totalPax() * 3;
                            $balance = $bookingTransaction->rate - ($service_charge + $tgst + $green_tax);
                        @endphp
                        <tr>
                            <td class="py-1 align-top">
                                {{ $bookingTransaction->date->format('d/m/Y') }}
                            </td>
                            <td class="py-1 capitalize">
                                <div class="font-medium">
                                    {{ str($bookingTransaction->transaction_type)->replace('_', ' ') }}</div>
                                <div class="mt-1 indent-5">
                                    <div class="text-xs">Service Charge: {{ number_format($service_charge, 2) }}
                                    </div>
                                    <div class="text-xs">T-GST: {{ number_format($tgst, 2) }}
                                    </div>
                                    <div class="text-xs">Green Tax: {{ number_format($green_tax, 2) }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-1 text-right align-top">
                                <div>{{ number_format($balance, 2) }}</div>
                                <div class="mt-1">
                                    <div class="text-xs">{{ number_format($service_charge, 2) }}
                                    </div>
                                    <div class="text-xs">{{ number_format($tgst, 2) }}
                                    </div>
                                    <div class="text-xs">{{ number_format($green_tax, 2) }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-1 text-right align-top">{{ $bookingTransaction->rate }}</td>
                        </tr>
                    @endforeach
                    {{-- <tr>
                        <td class="py-1 border-t border-gray-400"></td>
                        <td class="py-1 border-t border-gray-400"></td>
                        <td class="py-1 border-t border-gray-400"></td>
                        <td class="py-1 font-bold text-right border-t border-gray-400"><span
                                class="pr-4">Grand Total</span>{{ number_format($reservation->totalCharges(), 2) }}</td>
                    </tr> --}}
                </table>
            </div>

            <div class="grid grid-cols-4 border-t border-gray-400 divide-x divide-gray-400">
                <div class="col-span-3 p-3">
                  
                </div>
               
                <div class="grid grid-cols-2 divide-x divide-gray-400">
                    <div class="divide-y divide-gray-400 ">
                        <div class="p-3 font-bold">Grand Total</div>
                        <div class="p-3 font-bold">Total Paid</div>
                        <div class="p-3 font-bold">Balance</div>
                    </div>
                    <div class="text-right divide-y divide-gray-400">
                        <div class="p-3 font-bold">{{ number_format($reservation->totalCharges(), 2) }}</div>
                        <div class="p-3 font-bold "> {{ number_format($paid, 2) }}</div>
                        <div class="p-3 font-bold "> {{ number_format($reservation->totalCharges() - $paid, 2) }}</div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-layouts.pdf>
