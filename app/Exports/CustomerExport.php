<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    
    public $datas = null;

    public function __construct($props) {
        $this->datas = $props['datas'];
    }

    public function view(): View {
        return view('exports.customer', [
            'datas' => $this->datas
        ]);
    }
}
