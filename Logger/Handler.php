<?php

namespace SapientPro\Core\Logger;


/**
 * Class Handler
 * @package SapientPro\Core\Logger
 */
class Handler extends \Magento\Framework\Logger\Handler\Base implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/sapientpro.log';
}
