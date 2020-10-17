<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2019 SapientPro (https://sapient.pro)
 */

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
