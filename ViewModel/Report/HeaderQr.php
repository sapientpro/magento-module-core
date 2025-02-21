<?php

declare(strict_types=1);

namespace SapientPro\Core\ViewModel\Report;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use SapientPro\Core\Api\Pdf\PdfImagesServiceInterface;

class HeaderQr implements ArgumentInterface
{
    private PdfImagesServiceInterface $pdfImagesService;

    public function __construct(
        PdfImagesServiceInterface $pdfImagesService
    )
    {
        $this->pdfImagesService = $pdfImagesService;
    }

    public function generateQrCode($filePath)
    {
        return $this->pdfImagesService->generateQrCodeInBase64($filePath);
    }
}
