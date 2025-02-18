<?php

namespace SapientPro\Core\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use SapientPro\Core\Api\Pdf\PdfImagesServiceInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CompanyInfoProvider implements ArgumentInterface
{
    /**
     * @var PdfImagesServiceInterface
     */
    private PdfImagesServiceInterface $imagesService;

    /**
     * @var TimezoneInterface
     */
    private TimezoneInterface $timezone;

    /**
     * Constructor
     *
     * @param PdfImagesServiceInterface $imagesService
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        PdfImagesServiceInterface $imagesService,
        TimezoneInterface $timezone
    ) {
        $this->imagesService = $imagesService;
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getCompanyLogo(): string
    {
        return $this->imagesService->getPrintOutsPdfLogo();
    }

    /**
     * @return string
     */
    public function getCurrentCompanyDate(): string
    {
        return $this->timezone->date();
    }
}
