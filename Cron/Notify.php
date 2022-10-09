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
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class Notify
{
    const ENDPOINT = 'https://2020.sapient.tools/wp-json/myplugin/v1/magento-feed/';

    public function __construct(
        private readonly NotifierInterface $notifier,
        private readonly ZendClientFactory $httpClientFactory,
        private readonly Json              $jsonDecoder,
        private readonly LoggerInterface   $logger
    ) {
    }

    public function execute(): void
    {
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
            $this->logger->alert($exception->getMessage());
        }
    }

}
