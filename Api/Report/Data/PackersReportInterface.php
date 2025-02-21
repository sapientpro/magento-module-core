<?php

namespace SapientPro\Core\Api\Report\Data;

use Magento\Framework\DataObject;

interface PackersReportInterface
{
    /**
     * Add a packer to the report
     *
     * @param DataObject $packer
     * @return void
     */
    public function addPacker(DataObject $packer): void;

    /**
     * Get the list of packers
     *
     * @return array
     */
    public function getPackers(): array;
}
