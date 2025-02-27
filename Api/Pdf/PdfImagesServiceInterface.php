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

    /**
     * @return string
     */
    public function getPdfFilePath(): string;

    /**
     * @param string $filePath
     * @return void
     */
    public function setPdfFilePath(string $filePath): void;
}
