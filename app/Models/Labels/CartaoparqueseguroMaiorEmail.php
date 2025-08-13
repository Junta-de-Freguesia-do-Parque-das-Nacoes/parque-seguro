<?php

namespace App\Models\Labels;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Models\Labels\RectangleSheet;
use App\Models\Asset;

class CartaoparqueseguroMaiorEmail extends RectangleSheet
{
    private const BARCODE1D_SIZE = 0.15;
    private const BARCODE2D_SIZE = 0.76;
    private const BARCODE2D_MARGIN = 0.075;
    private const LOGO_SIZE = [0.75, 0.50];
    private const LOGO_MARGIN = 0.05;
    private const TEXT_MARGIN = 0.04;
    private const PAGE_MARGIN = 0.3; // Margem de 0.1 polegada em todos os lados
    private float $textSize;
    private float $labelWidth;
    private float $labelHeight;
    private float $labelSpacingH;
    private float $labelSpacingV;
    private float $pageMarginTop;
    private float $pageMarginBottom;
    private float $pageMarginLeft;
    private float $pageMarginRight;
    private float $pageWidth;
    private float $pageHeight;
    private int $columns;
    private int $rows;

    public function __construct()
    {
        $settings = Setting::getSettings();
        $this->textSize = Helper::convertUnit($settings->labels_fontsize, 'pt', 'in');
        $this->labelWidth   = 3.34; // Tamanho do cartão
        $this->labelHeight  = 2.17; // Tamanho do cartão
        $this->labelSpacingH = 0; // Sem espaçamento, apenas um cartão por página
        $this->labelSpacingV = 0; // Sem espaçamento, apenas um cartão por página
        $this->pageMarginTop     = self::PAGE_MARGIN;
        $this->pageMarginBottom = self::PAGE_MARGIN;
        $this->pageMarginLeft   = self::PAGE_MARGIN;
        $this->pageMarginRight  = self::PAGE_MARGIN;
        $this->pageWidth    = 3.34 + (2 * self::PAGE_MARGIN); // Tamanho do cartão + margens
        $this->pageHeight   = 2.17 + (2 * self::PAGE_MARGIN); // Tamanho do cartão + margens
        $this->columns = 1; // Apenas um cartão por página
        $this->rows = 1; // Apenas um cartão por página
    }

    public function getUnit()       { return 'in'; }
    public function getPageWidth()      { return $this->pageWidth; }
    public function getPageHeight()     { return $this->pageHeight; }
    public function getPageMarginTop()      { return $this->pageMarginTop; }
    public function getPageMarginBottom()   { return $this->pageMarginBottom; }
    public function getPageMarginLeft()     { return $this->pageMarginLeft; }
    public function getPageMarginRight()    { return $this->pageMarginRight; }
    public function getColumns() { return $this->columns; }
    public function getRows()    { return $this->rows; }
    public function getLabelBorder() { return 0; }
    public function getLabelWidth()   { return $this->labelWidth; }
    public function getLabelHeight()  { return $this->labelHeight; }
    public function getLabelMarginTop()     { return 0; }
    public function getLabelMarginBottom()  { return 0; }
    public function getLabelMarginLeft()    { return 0; }
    public function getLabelMarginRight()   { return 0; }
    public function getLabelColumnSpacing() { return $this->labelSpacingH; }
    public function getLabelRowSpacing()    { return $this->labelSpacingV; }
    public function getSupportAssetTag()    { return false; }
    public function getSupport1DBarcode() { return true; }
    public function getSupport2DBarcode() { return true; }
    public function getSupportFields()      { return 2; }
    public function getSupportTitle()       { return true; }
    public function getSupportLogo()        { return true; }

    public function preparePDF($pdf)
    {
        // Configurar o estilo de linha tracejada para marcas de picotado
        $pdf->SetLineStyle([
            'width' => 0.01,
            'dash' => '2,2', // Padrão tracejado: 2 pontos ligado, 2 pontos desligado
            'color' => [0, 0, 0], // Cor preta
        ]);
    }

    public function write($pdf, $asset)
    {
        $settings = Setting::getSettings();
        $displayName = $asset->nome_apelido ?? $asset->name;
        $textY = $this->pageMarginTop; // Ajustar para margem
        $textX1 = $this->pageMarginLeft; // Ajustar para margem
        $textX2 = $this->pageMarginLeft + $this->getLabelWidth();
        $imagePath = '/var/www/html/snipeit/public/img/cartao-dos-alunos-maior.png';
        $originalWidth = 3.34;
        $originalHeight = 2.17;
        $newWidth = $originalWidth * 1.0;
        $newHeight = $originalHeight * 1.0;
        $pdf->Image($imagePath, $textX1, $textY, $newWidth, $newHeight);

        // Desenhar marcas de picotado ao redor do cartão
        $pdf->Rect(
            $this->pageMarginLeft,
            $this->pageMarginTop,
            $this->labelWidth,
            $this->labelHeight,
            'D' // Apenas desenhar (não preencher)
        );

        // Geração do código de barras 1D, se disponível
        if ($asset->barcode1d) {
            static::write1DBarcode(
                $pdf, $asset->barcode1d->content, $asset->barcode1d->type,
                $this->pageMarginLeft + 0.05,
                $this->pageMarginTop + $this->getLabelHeight() - self::BARCODE1D_SIZE,
                $this->getLabelWidth() - 0.1,
                self::BARCODE1D_SIZE
            );
        }

        // Escrever o nome do utente
        $pdf->SetTextColor(255, 255, 255);
        $textXPosition = $textX1 + 1.52;
        $pdf->SetXY($textXPosition, $textY);
        static::writeText(
            $pdf, $displayName, $textXPosition, $textY + 1.0,
            'freesans', 'B', $this->textSize * 1.3, 'L',
            $this->getLabelWidth() - 0.2, $this->textSize,
            true, 0
        );

        // Geração do QR code (com verificação para null)
        if ($asset->barcode2d !== null && is_array($asset->barcode2d)) {
            $newUrl = $asset->barcode2d['content'];
            $qrCodeType = $asset->barcode2d['type'];
        } else {
            $newUrl = "https://parque-seguro.jf-parquedasnacoes.pt:8126/confirmar-check/{$asset->id}";
            $qrCodeType = 'QRCODE,H';
            \Log::warning("Acessor barcode2d retornou null para o asset ID {$asset->id}. Usando fallback.");
        }

        $qrCodeYPosition = $textY + 0.34;
        $qrCodeXPosition = $textX1 + 0.13;
        $qrCodeSize = 1.3;
        static::write2DBarcode(
            $pdf, $newUrl, $qrCodeType,
            $qrCodeXPosition, $qrCodeYPosition, $qrCodeSize, $qrCodeSize
        );
        $textY = $qrCodeYPosition;

        // Escrever apenas o número do cartão e a escola/empresa
        $pdf->SetTextColor(255, 255, 255);
        $maxTextWidth = $this->getLabelWidth() - ($qrCodeXPosition - $textX1) - $qrCodeSize - 0.1;
        $textXPosition = $qrCodeXPosition + $qrCodeSize + 0.1;
        $pdf->SetXY($textXPosition, $textY);

        // Número do cartão (asset_tag)
        if ($asset->asset_tag) {
            static::writeText(
                $pdf, "Nº Cartão: {$asset->asset_tag}",
                $textXPosition, $textY + 1.0,
                'freesans', 'B', $this->textSize * 1.2, 'L',
                $maxTextWidth, $this->textSize,
                true, 0
            );
            $textY += $this->textSize + self::TEXT_MARGIN;
        }

        // Escola/Empresa (via company ou campo personalizado)
        $companyName = $asset->company ? $asset->company->name : 'N/A';
        if ($companyName !== 'N/A') {
            static::writeText(
                $pdf, "Escola: {$companyName}",
                $textXPosition, $textY + 1.0,
                'freesans', 'B', $this->textSize * 1.2, 'L',
                $maxTextWidth, $this->textSize,
                true, 0
            );
            $textY += $this->textSize + self::TEXT_MARGIN;
        }
    }
}