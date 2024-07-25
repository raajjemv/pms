<?php

namespace Database\Seeders;

use App\Models\RoomStatus;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoomStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $informations = [
            'Vacant room.',
            'A room that is currently occupied by a guest who is registered with the hotel.',
            'A room that is currently occupied by a guest who is registered with the hotel in a clean room.',
            'A room that is currently occupied by a guest who is registered with the hotel in a dirty room. This happens due to a status change from OC to OD after one night stay.',
            'An empty room that has been cleaned and inspected by the floor supervisor and is ready for guests (for sale).',
            'An empty room in a clean state.',
            'An empty room in a dirty state. A dirty room can occur due to a guest checking out or a cleaning program from housekeeping.',
            'A room that is registered by a guest, but the room is free of charge (free).',
            'A room that is registered but used by hotel management.',
            'A room with this sign means that the guest does not want to be disturbed.',
            'A guest who is still registered, but the room is not in use because the guest has to leave the hotel for a few days or the guest is sleeping outside the hotel area.',
            'Guest leaves hotel before settling all outstanding obligations.',
            'Room status under repair.',
            'A room that requires serious repairs, usually takes more than one day to repair. This status can occur due to damage to the room or a cleaning program from housekeeping. Out of order reduces room availability.',
            'List of rooms expected to check out today according to departure date.',
            'List of names of guests expected to arrive today.',
            'Guest who has left the hotel today after settling all outstanding obligations including returning the used key to the front office.',
            'Guest\'s request to leave the hotel later than the specified check-out time.',
            'A guest who is still registered in a room without any belongings in it.',
            'Guest\'s request to the hotel to double lock so that no one can enter the room.',
        ];

        $names = [
            'Vacant',
            'Occupied',
            'Occupied Clean',
            'Occupied Dirty',
            'Vacant Clean Inspected',
            'Vacant Clean',
            'Vacant Dirty',
            'Compliment',
            'House Use',
            'Do not Disturb',
            'Sleep Out',
            'Skipper',
            'Out of Service',
            'Out of Order',
            'Due Out / Expected Departure',
            'Expected Arrival',
            'Check Out',
            'Late Check Out',
            'Occupeid no Luggage',
            'Double Lock',
        ];

        $codes = [
            'V',
            'O',
            'OC',
            'OD',
            'VCI',
            'VC',
            'VD',
            'Comp',
            'HU',
            'DND',
            'SO',
            'Skip',
            'OS',
            'OOO',
            'DO/ED',
            'EA',
            'CO',
            'LCO',
            'ONL',
            'DL',
        ];

        for ($i = 0; $i < count($codes); $i++) {
            RoomStatus::create([
                'tenant_id' => 1001,
                'name' => $names[$i],
                'code' => $codes[$i],
                'description' => $informations[$i],
            ]);
        }
    }
}
