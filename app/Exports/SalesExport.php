<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SalesExport implements FromView, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $user = null;
    public $period = null;
    public $datas = null;

    public function __construct($props) {
        $this->datas = $props['datas'];
        $this->user = $props['user'];
        $this->period = $props['period'];
    }
    
    public function view(): View {
        return view('exports.sales', [
            'datas' => $this->datas,
            'period' => $this->period,
            'user' => $this->user
        ]);
    }
}
