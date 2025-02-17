<?php

namespace SapientPro\Core\Api\Pdf;

interface PdfImagesServiceInterface
{
    /**
     * Get PDF images in base64 format
     *
     * @return string
     */
    public function getPrintOutsPdfLogo(): string;

    /**
     * GR code generator for PDF in base64 format
     *
     * @param string $data
     * @return string
     */
    public function generateQrCodeInBase64(string $data): string;

}
