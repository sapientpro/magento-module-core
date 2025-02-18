<?php

namespace SapientPro\Core\Service\Pdf;

use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem;
use SapientPro\Core\Api\Pdf\PdfImagesServiceInterface;

class PdfImagesService implements PdfImagesServiceInterface
{
    const EMPTY_IMAGE_FORMAT = 'data:image/png;base64,';

    const EMPTY_IMAGE = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/wcAAwAB/epT/gAAAABJRU5ErkJggg==';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var WriteInterface
     */
    private WriteInterface $mediaDirectory;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $filesystem
     * @throws FileSystemException
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Filesystem $filesystem
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function getPrintOutsPdfLogo(): string
    {
        $logo = $this->scopeConfig->getValue('sales/identity/logo', ScopeInterface::SCOPE_STORE);
        $imagePath = 'sales/store/logo/' . $logo;

        if ($this->mediaDirectory->isFile($imagePath)) {
            return $this->getBase64Image(
                $this->mediaDirectory->getAbsolutePath($imagePath)
            );
        }

        return self::EMPTY_IMAGE_FORMAT . self::EMPTY_IMAGE;
    }

    public function generateQrCodeInBase64(string $data): string
    {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'quality' => 100,
            'keepAsSquare' => [
                QRMatrix::M_FINDER_DARK,
                QRMatrix::M_FINDER_DOT,
                QRMatrix::M_ALIGNMENT_DARK,
            ],
        ]);

        $qrcode = new QRCode($options);
        return $qrcode->render($data);
    }

    /**
     * Convert image to base64
     *
     * @param string $absolutePath
     * @return string
     */
    private function getBase64Image(string $absolutePath): string
    {
        $imageData = base64_encode(file_get_contents($absolutePath));
        $imageType = pathinfo($absolutePath, PATHINFO_EXTENSION);

        return 'data:image/' . $imageType . ';base64,' . $imageData;
    }

}
