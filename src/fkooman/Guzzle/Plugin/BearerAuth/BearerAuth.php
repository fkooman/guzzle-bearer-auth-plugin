<?php

namespace fkooman\Guzzle\Plugin\BearerAuth;

use Guzzle\Common\Event;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use fkooman\Guzzle\Plugin\BearerAuth\Exception\BearerErrorResponseException;

class BearerAuth implements EventSubscriberInterface
{
    private $bearerToken;

    public function __construct($bearerToken)
    {
        $this->bearerToken = $bearerToken;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'request.before_send' => 'onRequestBeforeSend',
            'request.exception' => 'onRequestException'
        );
    }

    public function onRequestBeforeSend(Event $event)
    {
        $event['request']->setHeader("Authorization", sprintf("Bearer %s", $this->bearerToken));
    }

    public function onRequestException(Event $event)
    {
        if (null !== $event['response']->getHeader("WWW-Authenticate")) {
            throw BearerErrorResponseException::factory($event['request'], $event['response']);
        }
        throw BadResponseException::factory($event['request'], $event['response']);
    }
}
