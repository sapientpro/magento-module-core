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

/**
 *  Locale view model.
 */
class Locale extends DataObject implements ArgumentInterface
{
    /**
     * @var LocaleResolverInterface
     */
    private $localeResolver;

    /**
     * @param LocaleResolverInterface $localeResolver
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(LocaleResolverInterface $localeResolver)
    {
        $this->localeResolver = $localeResolver;
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->localeResolver->getDefaultLocale();
    }

    /**
     * @return mixed
     */
    public function getLocaleCode()
    {
        return $this->localeResolver->getLocale();
    }

}
