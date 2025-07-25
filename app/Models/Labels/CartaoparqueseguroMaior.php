<?php

namespace App\Models\Labels;

use App\Helpers\Helper;
use App\Models\Setting;

class CartaoparqueseguroMaior extends RectangleSheet
{
    private const BARCODE1D_SIZE = 0.15;

    private const BARCODE2D_SIZE = 0.76;
    private const BARCODE2D_MARGIN = 0.075;

    private const LOGO_SIZE = [0.75, 0.50];
    private const LOGO_MARGIN = 0.05;

    private const TEXT_MARGIN = 0.04;


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


    public function __construct() {
        $settings = Setting::getSettings();
    
        $this->textSize = Helper::convertUnit($settings->labels_fontsize, 'pt', 'in');
    
        $this->labelWidth  = $settings->labels_width;
        $this->labelHeight = $settings->labels_height;
    
        // 🔹 Ajusta os espaçamentos horizontal e vertical para evitar sobreposição
        $this->labelSpacingH = 0.25; // Aumenta para mais espaço horizontal entre etiquetas
        $this->labelSpacingV = 0.74; // Aumenta para mais espaço vertical entre etiquetas
    
        // 🔹 Ajusta as margens da página para evitar cortes
        $this->pageMarginTop    = 0.3;  // Espaço extra no topo
        $this->pageMarginBottom = 0.0;  // Espaço extra no fundo
        $this->pageMarginLeft   = 0.75;  // Espaço extra na esquerda
        $this->pageMarginRight  = 0.6;  // Espaço extra na direita
    
        $this->pageWidth  = $settings->labels_pagewidth;
        $this->pageHeight = $settings->labels_pageheight;
    
        // 🔹 Calcula o espaço utilizável para etiquetas
        $usableWidth = $this->pageWidth - $this->pageMarginLeft - $this->pageMarginRight;
        $usableHeight = $this->pageHeight - $this->pageMarginTop - $this->pageMarginBottom;
    
        // 🔹 Ajusta o número de colunas e linhas, garantindo que não fique negativo
        $this->columns = floor($usableWidth / ($this->labelWidth + $this->labelSpacingH));
        $this->rows = floor($usableHeight / ($this->labelHeight + $this->labelSpacingV));
    
        // 🔹 Garante que pelo menos 1 etiqueta seja impressa
        if ($this->columns < 1) {
            $this->columns = 1;
        }
    
        if ($this->rows < 1) {
            $this->rows = 1;
        }
    }
    

    public function getUnit()   { return 'in'; }

    public function getPageWidth()  { return $this->pageWidth; }
    public function getPageHeight() { return $this->pageHeight; }

    public function getPageMarginTop()    { return $this->pageMarginTop; }
    public function getPageMarginBottom() { return $this->pageMarginBottom; }
    public function getPageMarginLeft()   { return $this->pageMarginLeft; }
    public function getPageMarginRight()  { return $this->pageMarginRight; }

    public function getColumns() { return $this->columns; }
    public function getRows()    { return $this->rows; }
    public function getLabelBorder() { return 0; }

    public function getLabelWidth()  { return $this->labelWidth; }
    public function getLabelHeight() { return $this->labelHeight; }

    public function getLabelMarginTop()    { return 0; }
    public function getLabelMarginBottom() { return 0; }
    public function getLabelMarginLeft()   { return 0; }
    public function getLabelMarginRight()  { return 0; }

    public function getLabelColumnSpacing() { return $this->labelSpacingH; }
    public function getLabelRowSpacing()    { return $this->labelSpacingV; }

    public function getSupportAssetTag()  { return false; }
    public function getSupport1DBarcode() { return true; }
    public function getSupport2DBarcode() { return true; }
    public function getSupportFields()    { return 4; }
    public function getSupportTitle()     { return true; }
    public function getSupportLogo()      { return true; }

    public function preparePDF($pdf) {}

    public function write($pdf, $record) {
        $asset = $record->get('asset');
        $settings = Setting::getSettings();

        // Pegar o nome completo do aluno
        $fullName = $asset->name;
        $nameParts = explode(" ", $fullName);
        $firstName = $nameParts[0];
        $lastName = end($nameParts);

        // Exibir apenas o primeiro e o último nome na etiqueta
        $displayName = $firstName . ' ' . $lastName;

        // Inicializa as variáveis de posição
        $textY = 0;
        $textX1 = 0;
        $textX2 = $this->getLabelWidth();

        // Adiciona a imagem de fundo
        $imagePath = '/var/www/html/snipeit/public/img/cartao-dos-alunos-maior.png'; // Caminho para a imagem
        $originalWidth = 3.34; // Largura da imagem em inches
        $originalHeight = 2.17; // Altura da imagem em inches
        $newWidth = $originalWidth * 1.0;  // Tamanho ajustado (100% do original)
        $newHeight = $originalHeight * 1.0;  // Tamanho ajustado (100% do original)

        $pdf->Image($imagePath, $textX1, $textY, $newWidth, $newHeight);

        // 1D Barcode
        if ($record->get('barcode1d')) {
            static::write1DBarcode(
                $pdf, $record->get('barcode1d')->content, $record->get('barcode1d')->type,
                0.05, $this->getLabelHeight() - self::BARCODE1D_SIZE,
                $this->getLabelWidth() - 0.1, self::BARCODE1D_SIZE
            );
        }

        // Exibe o nome no label
        $pdf->SetTextColor(255, 255, 255); 
        $textXPosition = $textX1 + 1.52;  // Ajuste a posição X
        $pdf->SetXY($textXPosition, $textY);  // Define a posição do nome
        static::writeText(
            $pdf, $displayName, $textXPosition, $textY + 1.0, // Posição ajustada
            'freesans', 'B', $this->textSize * 1.3, 'L',
            $this->getLabelWidth() - 0.2, $this->textSize,  // Largura ajustada para o nome
            true, 0
        );

        // 2D Barcode (QR Code)
        if ($record->get('barcode2d')) {
            $newUrl = "https://parque-seguro.jf-parquedasnacoes.pt:8126/confirmar-check/{$record->get('asset')->id}";
            $qrCodeYPosition = $textY + 0.34;
            $qrCodeXPosition = $textX1 + 0.13;
            $qrCodeSize = 1.3;

            static::write2DBarcode(
                $pdf, $newUrl, $record->get('barcode2d')->type,
                $qrCodeXPosition, $qrCodeYPosition, $qrCodeSize, $qrCodeSize
            );

            $textY = $qrCodeYPosition;  
        }

        // Logo
        // Se houver logo, pode adicionar aqui

        // Renderizar os campos com seus rótulos
        $fieldsDone = 0;
        if ($fieldsDone < $this->getSupportFields()) {
            foreach ($record->get('fields') as $field) {
                $pdf->SetTextColor(255, 255, 255);  // Cor do texto (branco)

                $maxTextWidth = $this->getLabelWidth() - $qrCodeXPosition - $qrCodeSize - 0.1;
                $textXPosition = $qrCodeXPosition + $qrCodeSize + 0.1;
                $pdf->SetXY($textXPosition, $textY);
                static::writeText(
                    $pdf, (($field['label']) ? $field['label'].' ' : '') . $field['value'],
                    $textXPosition, $textY + 1.0,
                    'freesans', 'B', $this->textSize *1.2, 'L',
                    $maxTextWidth, $this->textSize, 
                    true, 0
                );

                $textY += $this->textSize + self::TEXT_MARGIN;
                $fieldsDone++;
            }
        }
    }
}
