<?php

namespace App\Models\Labels;

use App\Helpers\Helper;
use App\Models\Setting;

class DefaultLabel extends RectangleSheet
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

        $this->labelSpacingH = $settings->labels_display_sgutter;
        $this->labelSpacingV = $settings->labels_display_bgutter;

        $this->pageMarginTop    = $settings->labels_pmargin_top;
        $this->pageMarginBottom = $settings->labels_pmargin_bottom;
        $this->pageMarginLeft   = $settings->labels_pmargin_left;
        $this->pageMarginRight  = $settings->labels_pmargin_right;

        $this->pageWidth  = $settings->labels_pagewidth;
        $this->pageHeight = $settings->labels_pageheight;

        $usableWidth = $this->pageWidth - $this->pageMarginLeft - $this->pageMarginRight;
        $usableHeight = $this->pageHeight - $this->pageMarginTop - $this->pageMarginBottom;

        $this->columns = ($usableWidth + $this->labelSpacingH) / ($this->labelWidth + $this->labelSpacingH);
        $this->rows = ($usableHeight + $this->labelSpacingV) / ($this->labelHeight + $this->labelSpacingV);

        // Make sure the columns and rows are never zero, since that scenario should never happen
        if ($this->columns == 0) {
            $this->columns = 1;
        }

        if ($this->rows == 0) {
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
        $fullName = $asset->name; // O nome completo do aluno
    
        // Dividir o nome completo em partes
        $nameParts = explode(" ", $fullName);
    
        // Pegar o primeiro e o último nome
        $firstName = $nameParts[0];
        $lastName = end($nameParts);
    
        // Exibir apenas o primeiro e o último nome na etiqueta
        $displayName = $asset->nome_apelido;
    
        // Inicializa as variáveis de posição
        $textY = 0;
        $textX1 = 0;
        $textX2 = $this->getLabelWidth();
    
        // Adiciona a imagem de fundo
        $imagePath = '/var/www/html/snipeit/public/img/cartao-dos-alunos.png'; // Caminho para a imagem
        $originalWidth = 3.11; // Largura da imagem em inches
        $originalHeight = 1.46; // Altura da imagem em inches
        $newWidth = $originalWidth * 1.0;  // Tamanho ajustado (100% do original)
        $newHeight = $originalHeight * 1.0;  // Tamanho ajustado (100% do original)
        
        $pdf->Image($imagePath, $textX1, $textY, $newWidth, $newHeight);
        
        // Desenha o contorno da etiqueta (caixa retangular)
        $pdf->SetDrawColor(0, 0, 0);  // Cor do contorno (preto)
        //$pdf->Rect($textX1, $textY, $this->labelWidth, $this->labelHeight);
        
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
   $textXPosition = $textX1 + 1.4;  // Ajuste a posição X
   $pdf->SetXY($textXPosition, $textY);  // Define a posição do nome
   static::writeText(
       $pdf, $displayName, $textXPosition, $textY + 0.7, // Posição ajustada
       'freesans', 'B', $this->textSize, 'L',
       $this->getLabelWidth() - 0.2, $this->textSize,  // Largura ajustada para o nome
       true, 0
   );
        
        // 2D Barcode (QR Code)
        if ($record->get('barcode2d')) {
            $newUrl = "https://parque-seguro.jf-parquedasnacoes.pt:8126/confirmar-check/{$record->get('asset')->id}";
            $qrCodeYPosition = $textY + 0.1;  // Ajuste a posição Y do QR Code
            $qrCodeXPosition = $textX1 + 0.1;  // Posição X para o QR Code (à esquerda)
            $qrCodeSize = 1.2;  // Tamanho do QR Code
        
            static::write2DBarcode(
                $pdf, $newUrl, $record->get('barcode2d')->type,
                $qrCodeXPosition, $qrCodeYPosition, $qrCodeSize, $qrCodeSize
            );
        
            // Atualiza a posição Y para o texto, mas mantendo o QR Code na mesma linha
            $textY = $qrCodeYPosition;  // A posição Y não muda, o texto vai ao lado
        }
    
        // Logo
        // Se houver logo, pode adicionar aqui
        
        $textW = $textX2 - $textX1;

   
    
        // Renderizar os campos com os seus rótulos
        $fieldsDone = 0;
        if ($fieldsDone < $this->getSupportFields()) {
            foreach ($record->get('fields') as $field) {
    
                
                $pdf->SetTextColor(255, 255, 255);  // Cor do texto (branco)

       
    
                
        // Verificar se há espaço suficiente para o texto ao lado do QR Code
        $maxTextWidth = $this->getLabelWidth() - $qrCodeXPosition - $qrCodeSize - 0.1;  // Largura disponível para o texto ao lado do QR Code
    
        // Posicionando o texto do campo **ao lado do QR Code**
        $textXPosition = $qrCodeXPosition + $qrCodeSize + 0.1; // Posiciona à direita do QR Code
        $pdf->SetXY($textXPosition, $textY);  // Define a posição para o texto ao lado do QR Code
        static::writeText(
            $pdf, (($field['label']) ? $field['label'].' ' : '') . $field['value'],
            $textXPosition, $textY + 0.8,  // Ajuste da posição X para ao lado do QR Code
            'freesans', 'B', $this->textSize, 'L',
            $maxTextWidth, $this->textSize,  // Ajuste da largura disponível
            true, 0
        );

                   

        // Atualiza a posição Y após o texto (não muda, já está na mesma linha)
        $textY += $this->textSize + self::TEXT_MARGIN;  // Move para o próximo texto, se houver
        $fieldsDone++;
    }
}


    
        // Atualiza a posição Y para o próximo conteúdo
        $textY += $this->textSize + self::TEXT_MARGIN;
    }
    
    
    
    
    

}

?>