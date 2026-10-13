<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Pacel\TempolaryPacel;

class GetPacel extends Component
{

    public $mizigo = 0;

    public function render()
    {

        $this->mizigo = TempolaryPacel::all()->where('added_by',auth()->user()->id);

        return view('livewire.get-pacel');
    }
}
