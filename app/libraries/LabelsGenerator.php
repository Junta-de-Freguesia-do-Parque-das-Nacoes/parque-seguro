<?php

namespace App\Libraries;

use App\Models\Labels\CartaoparqueseguroMaiorEmail;
use TCPDF;

class LabelsGenerator
{
    protected $labelModel;
    protected $assets = [];
    protected TCPDF $pdf;
    protected int $labels_per_page;
    protected int $current_label;

     public function __construct(CartaoparqueseguroMaiorEmail $labelModel)
    {
        $this->labelModel = $labelModel;
        $this->pdf = new TCPDF(
            $this->labelModel->getPageWidth() > $this->labelModel->getPageHeight() ? 'L' : 'P',
            $this->labelModel->getUnit(),
            [$this->labelModel->getPageWidth(), $this->labelModel->getPageHeight()],
            true,
            'UTF-8',
            false
        );

        $this->pdf->SetMargins(
            $this->labelModel->getPageMarginLeft(),
            $this->labelModel->getPageMarginTop(),
            $this->labelModel->getPageMarginRight()
        );

        $this->pdf->SetAutoPageBreak(
            true,
            $this->labelModel->getPageMarginBottom()
        );

        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor(config('app.name'));
        $this->pdf->SetTitle('Labels');
        $this->labels_per_page = $this->labelModel->getColumns() * $this->labelModel->getRows();
        $this->current_label = 0;
        $this->addPage();
    }

    public function addPage()
    {
        $this->pdf->AddPage();
        $this->labelModel->preparePDF($this->pdf);
        $this->current_label = 0;
    }

    public function addAsset($asset)
    {
        if ($this->current_label >= $this->labels_per_page) {
            $this->addPage();
        }

        $this->assets[] = $asset;
        $this->writeLabel($asset);
    }

    public function output($format = 'S')
    {
        return $this->pdf->Output('labels.pdf', $format);
    }

    public function writeLabel($asset)
    {
        $x = $this->labelModel->getPageMarginLeft() +
            ($this->current_label % $this->labelModel->getColumns()) *
            ($this->labelModel->getLabelWidth() + $this->labelModel->getLabelColumnSpacing());
        $y = $this->labelModel->getPageMarginTop() +
            floor($this->current_label / $this->labelModel->getColumns()) *
            ($this->labelModel->getLabelHeight() + $this->labelModel->getLabelRowSpacing());

        $this->pdf->setXY($x, $y);
        // CORREÇÃO AQUI: Passamos o objeto `asset` para o método write
        $this->labelModel->write($this->pdf, $asset);
        $this->current_label++;
    }
}