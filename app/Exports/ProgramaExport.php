<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProgramaExport implements FromView
{
    protected $assets;
    protected $programa;
    protected $programaLabel;

    public function __construct($assets, $programa, $programaLabel)
    {
        $this->assets = $assets;
        $this->programa = $programa;
        $this->programaLabel = $programaLabel;
    }

    public function view(): View
    {
        return view('programas.exports.lista', [
            'assets' => $this->assets,
            'programa' => $this->programa,
            'programaLabel' => $this->programaLabel
        ]);
    }
}


