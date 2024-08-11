@props(['booking' => null])
<div>
    <x-slot name="heading">
        <div>
            <div class="flex items-center space-x-3">
                <svg fill="none" class="size-10" version="1.1" id="Layer_1" stroke-width="10" stroke="currentColor"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                    xml:space="preserve">
                    <g>
                        <g>
                            <path d="M393.892,120.137v30.417v331.029V512h102.899H512V120.137H393.892z M452.946,415.431h-30.417v-35.824h30.417V415.431z
M452.946,350.671h-30.417v-35.824h30.417V350.671z M452.946,287.133h-30.417v-35.824h30.417V287.133z M452.946,223.595h-30.417
V187.77h30.417V223.595z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path d="M148.526,0v30.417v89.719v361.446v30.417h35.994h142.961h35.994v-30.416V120.137v-89.72V0H148.526z M211.838,62.344
h30.417v31.6h27.49v-31.6h30.417v93.617h-30.417v-31.599h-27.49v31.599h-30.417V62.344z M240.792,287.133v-35.824h30.417v35.824
H240.792z M271.209,314.847v35.824h-30.417v-35.824H271.209z M240.792,223.595V187.77h30.417v35.825H240.792z M184.52,187.77
h30.417v35.825H184.52V187.77z M184.52,251.308h30.417v35.824H184.52V251.308z M184.52,314.847h30.417v35.824H184.52V314.847z
M327.481,481.583h-30.417v-70.117h-25.855v70.117h-30.417v-70.117h-25.855v70.117H184.52V381.049h142.961V481.583z
M327.481,350.671h-30.417v-35.824h30.417V350.671z M327.481,287.133h-30.417v-35.824h30.417V287.133z M327.481,223.595h-30.417
V187.77h30.417V223.595z" />
                        </g>
                    </g>
                    <g>
                        <g>
                            <path
                                d="M0,120.137V512h15.209h102.9v-30.417V150.554v-30.417H0z M89.472,415.431H59.055v-35.824h30.417V415.431z M89.472,350.671
H59.055v-35.824h30.417V350.671z M89.472,287.133H59.055v-35.824h30.417V287.133z M89.472,223.595H59.055V187.77h30.417V223.595z" />
                        </g>
                    </g>
                </svg>
                <div>
                    <div>{{ $booking?->customer?->name }}</div>
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>

                        <div class="text-sm font-thin">{{ $booking?->customer->country }}</div>
                    </div>
                </div>

            </div>

        </div>
    </x-slot>

    <div>
        <table class="w-full">
            <tr>
                <td class="px-2 py-3">
                    <div class="text-xs">Booking Type</div>
                    <div class="text-sm font-medium">{{ str($booking?->booking_type)->title() }}</div>
                </td>

            </tr>
            <tr>
                <td class="px-2 py-3">
                    <div class="text-xs">Booking Number</div>
                    <div class="text-sm font-medium">{{ $booking?->booking_number }}</div>
                </td>
                <td class="px-2 py-3">
                    <div class="text-xs">Status</div>
                    <div class="text-sm font-medium">{{ $booking?->status }}</div>
                </td>
            </tr>
            <tr>
                <td class="px-2 py-3">
                    <div class="text-xs">Arrival Date</div>
                    <div class="text-sm font-medium">{{ $booking?->from->format('jS M Y') }}</div>
                </td>
                <td class="px-2 py-3">
                    <div class="text-xs">Departure Date</div>
                    <div class="text-sm font-medium">{{ $booking?->to->format('jS M Y') }}</div>
                </td>
            </tr>
            <tr>
                <td class="px-2 py-3">
                    <div class="text-xs">Booking Date</div>
                    <div class="text-sm font-medium">{{ $booking?->created_at->format('jS M Y | H:i') }}</div>
                </td>
                <td class="px-2 py-3">
                    <div class="text-xs">Room Category</div>
                    <div class="text-sm font-medium">{{ $booking?->room?->roomType->name }}</div>
                </td>
            </tr>
            <tr>
                <td class="px-2 py-3">
                    <div class="text-xs">Room Number</div>
                    <div class="text-sm font-medium">{{ $booking?->room->room_number }}</div>
                </td>
                <td class="px-2 py-3">
                    <div class="text-xs">Rate Plan</div>
                    <div class="text-sm font-medium">-</div>
                </td>
            </tr>
            <tr>
                <td class="px-2 py-3">
                    <div class="text-xs">Room Rate (avg)</div>
                    <div class="text-sm font-medium">USD {{ $booking?->averageRate() }}</div>
                </td>
                <td class="px-2 py-3">
                    <div class="text-xs">Pax</div>
                    <div class="flex items-end space-x-3 text-sm">
                        <div class="flex items-end">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg" > 
                                <path
                                    d="M10.2 0.800003C8.9875 0.800003 8 1.7875 8 3C8 4.2125 8.9875 5.2 10.2 5.2C11.4125 5.2 12.4 4.2125 12.4 3C12.4 1.7875 11.4125 0.800003 10.2 0.800003ZM8.2 5.6C6.9875 5.6 6 6.5875 6 7.8V12.8C6 13.2422 6.35938 13.6 6.8 13.6C7.24063 13.6 7.6 13.2422 7.6 12.8V8.9875C7.6 8.87969 7.69219 8.7875 7.8 8.7875C7.90781 8.7875 8 8.87969 8 8.9875V18.125C8 18.75 8.33594 19.2 8.9625 19.2C9.55469 19.2 10 18.7406 10 18.125V12.9875C10 12.8766 10.0891 12.7875 10.2 12.7875C10.3109 12.7875 10.4 12.8766 10.4 12.9875V18.2375C10.4016 18.2406 10.4109 18.2344 10.4125 18.2375C10.4672 18.7906 10.8844 19.2 11.4375 19.2C12.0625 19.2 12.4 18.75 12.4 18.125V9.0625C12.4 8.95469 12.4922 8.8625 12.6 8.8625C12.7078 8.8625 12.8 8.95469 12.8 9.0625V12.8C12.8 13.2422 13.1594 13.6 13.6 13.6C14.0406 13.6 14.4 13.2422 14.4 12.8V7.8C14.4 6.5875 13.4125 5.6 12.2 5.6H8.2Z"
                                    fill="currentColor" />
                            </svg>
                            <span class="">{{ $booking?->adults }}</span>
                        </div>
                        <div class="flex items-end ">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.1215 4.2C9.1111 4.2 8.29221 5.01888 8.29221 6.02927C8.29221 7.03965 9.1111 7.85853 10.1215 7.85853C11.1319 7.85853 11.9508 7.03965 11.9508 6.02927C11.9508 5.01888 11.1319 4.2 10.1215 4.2ZM9.52554 8.59024C9.04679 8.59024 8.62377 8.80461 8.3308 9.13616L5.25248 11.9787C4.93379 12.2745 4.91378 12.7733 5.20961 13.0934C5.50401 13.4121 6.00277 13.4321 6.32289 13.1363L7.92636 11.6557V13.8951L7.62768 18.2239C7.59052 18.7527 8.00925 19.2 8.53802 19.2C9.0182 19.2 9.4155 18.8284 9.44837 18.3497L9.66702 15.1756H10.5759L10.7946 18.3497C10.8275 18.8284 11.2248 19.2 11.7049 19.2C12.2337 19.2 12.6524 18.7527 12.6153 18.2239L12.3166 13.8908V11.6557L13.9201 13.1363C14.2402 13.4321 14.739 13.4121 15.0334 13.0934C15.3292 12.7733 15.3092 12.2745 14.9905 11.9787L11.9122 9.13616C11.6192 8.80461 11.1962 8.59024 10.7174 8.59024H9.52554Z"
                                    fill="currentColor" />
                            </svg>

                            <span class="">{{ $booking?->children }}</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
