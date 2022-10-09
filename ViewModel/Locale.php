<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2020 SapientPro (https://sapient.pro)
 */

namespace SapientPro\Core\ViewModel;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use \Magento\Framework\Locale\ResolverInterface as LocaleResolverInterface;

class Locale extends DataObject implements ArgumentInterface
{
    public function __construct(
        private readonly LocaleResolverInterface $localeResolver
    ) {
    }

    public function getDefaultLocale(): string
    {
        return $this->localeResolver->getDefaultLocale();
    }

    public function getLocaleCode(): string
    {
        return $this->localeResolver->getLocale();
    }
}
