<?php

namespace SapientPro\Core\Service\Pdf;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use SapientPro\Core\Api\ViewModel\ReportInterface as ViewModelReportInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\View\Result\Page;
use Dompdf\Options;
use Dompdf\Dompdf;

class DocumentPdfGenerator
{
    /**
     * Report root template
     */
    private const REPORT_ROOT_TEMPLATE = 'SapientPro_Core::report/report_root.phtml';

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var File
     */
    private File $fileIo;

    /**
     * @var string
     */
    private string $uniqueDocumentName;

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * @var Page
     */
    private Page $document;

    /**
     * Constructor
     *
     * @param PageFactory $pageFactory
     * @param Filesystem $filesystem
     * @param File $fileIo
     */
    public function __construct(
        PageFactory $pageFactory,
        Filesystem $filesystem,
        File $fileIo
    ) {
        $this->pageFactory = $pageFactory;
        $this->filesystem = $filesystem;
        $this->fileIo = $fileIo;
        $this->uniqueDocumentName = $this->generateUniqueDocumentName();
        $this->document = $this->pageFactory->create();
    }

    /**
     * Set document layout
     *
     * @param string $layout
     * @return $this
     */
    public function setDocumentLayout(string $layout): self
    {
        $this->document->addHandle($layout);
        return $this;
    }

    /**
     * @return ViewModelReportInterface
     * @throws LocalizedException
     */
    public function getDocumentViewModel(): ViewModelReportInterface
    {
        $block = $this->document->getLayout()->getBlock('report_details');
        $viewModel = $block->getData('view_model');

        if ($viewModel) {
            return $viewModel;
        }

        throw new LocalizedException(__('Block report_details not found'));
    }

    /**
     * Generate document HTML
     *
     * @return string
     */
    public function generateDocumentHtml(): string
    {
        $block = $this->document->getLayout()->createBlock(Template::class)
            ->setTemplate(self::REPORT_ROOT_TEMPLATE);

        /* @var $qrBlock Template */
        $qrBlock = $this->document->getLayout()->getBlock('report_qr');
        $qrBlock->setData('unique_document_url', $this->uniqueDocumentName);
        $block->setData('content', $this->document->getLayout()->getOutput());

        return $block->toHtml();
    }

    /**
     * Generate PDF document
     *
     * @throws LocalizedException
     * @throws FileSystemException
     */
    public function generatePdf(): void
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($this->generateDocumentHtml());
        $dompdf->setPaper('A4');
        $dompdf->render();

        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $filePath = date('Ymd') . $this->uniqueDocumentName . '.pdf';
        $fullPath = $varDir->getAbsolutePath($filePath);

        $this->fileIo->checkAndCreateFolder(dirname($fullPath));

        file_put_contents($fullPath, $dompdf->output());
    }

    /**
     * Generate unique document name
     */
    private function generateUniqueDocumentName(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Generate path for PDF
     *
     * @return WriteInterface
     * @throws FileSystemException
     */
    private function generatePathForPdf(): WriteInterface
    {
        return $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }
}
