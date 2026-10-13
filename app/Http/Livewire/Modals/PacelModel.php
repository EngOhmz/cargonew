<?php

namespace App\Http\Livewire\Modals;

use Livewire\Component;
use App\Models\Pacel\TempolaryPacel;

class PacelModel extends Component
{   
   // public $data;
    public $show;

    public $name;
    public $idadi;
    public $bei;
    public $ela_iliyopokelewa;
    public $jumla;
    public $mzigo_unapotoka;
    public $mzigo_unapokwenda;
    public $risiti;
    // public $receipt;

    protected $rules = [
        'idadi' => 'numeric',

    ];

    protected $messages = [
        'idadi.numeric' => 'Idadi  lazima ziwe namba',
    ];

    public function getTotal(){

        $this->validate();
        
        $this->jumla = $this->bei*$this->idadi;
    }

    public function get_idadi(){
        $this->idadi = 1;
    }

    protected $listeners = ['showModal' => 'showModal'];

    // public function mount($data) {
    //    // $this->data = $data;
    //     $this->show = false;
    // }

    public function submit(){

        $this->validate();

   
      $data = ['mzigo_unapokwenda'=>$this->mzigo_unapokwenda,
      'mzigo_unapotoka'=>$this->mzigo_unapotoka,'name'=>$this->name,
      'idadi'=>$this->idadi,'bei'=>$this->bei,'receipt'=>$this->receipt,
      'ela_iliyopokelewa'=>$this->ela_iliyopokelewa,
      'jumla'=>$this->jumla,
    'added_by'=>auth()->user()->id];

    if(!empty($data['idadi'])){
        $data['idadi'] = $this->idadi;
    }
    else{
        $data['idadi'] = 1;

    }
    if(!empty($data['mzigo_unapotoka'])){
        $data['mzigo_unapotoka'] = $this->mzigo_unapotoka;
    }
    else{
        $data['mzigo_unapotoka'] = "#";

    }
    if(!empty($data['ela_iliyopokelewa'])){
        $data['ela_iliyopokelewa'] = $this->ela_iliyopokelewa;
    }
    else{
        $data['ela_iliyopokelewa'] = "0";

    }
    if(!empty($data['jumla'])){
        $data['jumla'] = $this->jumla;
    }
    else{
        $data['jumla'] = "0";

    }
        
        TempolaryPacel::create($data);

        return redirect(route('dashboard.index'));

    }

    public $receipt = "R";

    
    public $showDiv = "yes";

    public function openDiv()
    {
        $this->showDiv ='no';
    }
    public function openDiv1()
    {
        $this->showDiv ='yes';
    }

    public function showModal($data) {
        $this->data = $data;

        $this->doShow();
    }

    public function doShow() {
        $this->show = true;
    }

    public function doClose() {
        $this->show = false;
    }

    public function doSomething() {
        // Do Something With Your Modal

        // Close Modal After Logic
        $this->doClose();
    }
    public function render()
    {
        return view('livewire.modals.pacel-model');
    }
}
