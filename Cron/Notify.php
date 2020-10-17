<?php
/**
 * SapientPro
 *
 * @category    SapientPro
 * @package     SapientPro_Core
 * @author      SapientPro Team <info@sapient.pro >
 * @copyright   Copyright © 2009-2019 SapientPro (https://sapient.pro)
 */

namespace SapientPro\Core\Cron;

use Magento\Framework\Notification\NotifierInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use SapientPro\Core\Logger\Handler;

/**
 * Class Notify
 * @package SapientPro\Core\Cron
 */
class Notify
{
    /**
     * News Feed
     */
    const ENDPOINT = 'http://2020.sapient.tools/wp-json/myplugin/v1/magento-feed/';

    /**
     * @var NotifierInterface
     */
    protected $notifier;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * @var Json
     */
    private $jsonDecoder;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Notify constructor.
     * @param NotifierInterface $notifier
     * @param ManagerInterface $messageManager
     * @param ZendClientFactory $httpClientFactory
     * @param Json $jsonDecoder
     * @param Handler $logger
     */
    public function __construct(
        NotifierInterface $notifier,
        ManagerInterface $messageManager,
        ZendClientFactory $httpClientFactory,
        Json $jsonDecoder,
        Handler $logger

    )
    {
        $this->notifier = $notifier;
        $this->messageManager = $messageManager;
        $this->httpClientFactory = $httpClientFactory;
        $this->jsonDecoder = $jsonDecoder;
        $this->logger = $logger;
    }

    /**
     * Check news updates
     */
    public function execute()
    {
        // Check updates
        try {
            $client = $this->httpClientFactory->create();
            $client->setUri(self::ENDPOINT . time());
            $client->setMethod('GET');
            $client->setHeaders('Content-Type', 'application/json');
            $client->setHeaders('Accept', 'application/json');
            $response = $this->jsonDecoder->unserialize($client->request()->getBody());

            // Add notices
            foreach ($response as $item) {
                switch ($item->status) {
                    case('critical'):
                        $this->notifier->addCritical(
                            $item->data->title,
                            $item->data->content,
                            $item->data->link->url
                        );
                        break;
                    case('major'):
                        $this->notifier->addMajor(
                            $item->data->title,
                            $item->data->content,
                            $item->data->link->url
                        );
                        break;
                    case('minor'):
                        $this->notifier->addMinor(
                            $item->data->title,
                            $item->data->content,
                            $item->data->link->url
                        );
                        break;
                    case('notice'):
                        $this->notifier->addNotice(
                            $item->data->title,
                            $item->data->content,
                            $item->data->link->url
                        );
                        break;
                }
            }
        } catch (\Zend_Http_Client_Exception $exception) {
            $this->logger->write($exception->getMessage());
        }
    }

}
