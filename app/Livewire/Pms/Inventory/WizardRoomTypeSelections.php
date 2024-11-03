<?php

namespace App\Livewire\Pms\Inventory;

use Livewire\Component;
use App\Models\RoomType;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Modelable;


class WizardRoomTypeSelections extends Component
{
    #[Modelable] 
    public $value;

//     public function updatedRoomTypeSelections()
//     {
//         // $this->dispatch('invetory-room-types', data: $this->roomTypeSelections);
// 
//     }

    public function render()
    {
        return view('livewire.pms.inventory.wizard-room-type-selections', [
            'roomTypes' => RoomType::with('ratePlans', 'ratePlanRoomType')->whereHas('ratePlans')->get()
        ]);
    }
}
