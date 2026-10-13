<?php

namespace App\Http\Livewire;

use Livewire\Component;

use App\Models\Pacel\TempolaryPacel;

class AddPacel extends Component
{

    public $count = 0;
    

    public $mizigo = 0;

    public function getMzigo(){
        
    }

 

    public function increment()

    {

        $this->count++;

    }
    
    public function render()
    {
        $this->mizigo = TempolaryPacel::all();
        return view('livewire.add-pacel');
    }
}
