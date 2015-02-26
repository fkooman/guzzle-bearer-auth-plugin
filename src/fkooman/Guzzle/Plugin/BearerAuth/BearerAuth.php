<?php

namespace fkooman\Guzzle\Plugin\BearerAuth;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\EmitterInterface;
use GuzzleHttp\Event\SubscriberInterface;
use fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;

class BearerAuth implements SubscriberInterface
{
    private $bearerToken;

    public function __construct($bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }

    public function getEvents()
    {
        return [
            'before' => ['onBefore', 100],
            'error'  => ['onError']
        ];
    }

    public function onBefore(BeforeEvent $event, $name)
    {
        if ($event !== null &&
            $event->getRequest() !== null) {
            $event->getRequest()->setHeader('Authorization', sprintf('Bearer %s', $this->bearerToken));
        }
    }

    public function onError(ErrorEvent $event)
    {
        if (!is_null($event) &&
            $event->hasResponse() &&
            $event->getResponse()->hasHeader('WWW-Authenticate')) {
            throw BearerErrorResponseException::factory($event->getRequest(), $event->getResponse());
        }
    }
}
