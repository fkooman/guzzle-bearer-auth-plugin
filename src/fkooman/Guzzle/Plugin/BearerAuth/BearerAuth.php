<?php

namespace fkooman\Guzzle\Plugin\BearerAuth;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Event;

class BearerAuth implements EventSubscriberInterface
{
    private $bearerToken;
    private $callback;

    public function __construct($bearerToken, $callback = null)
    {
        $this->bearerToken = $bearerToken;
        $this->callback = $callback;
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
        if (401 === $event['response']->getStatusCode()) {
            if (null !== $this->callback) {
                $header = $event['response']->getHeader("WWW-Authenticate");
                $reason = (1 === preg_match('/^Bearer.*?error="(.*?)".*$/', $header, $matches)) ? $matches[1] : "unknown";
                if (false === call_user_func($this->callback, $reason)) {
                    throw $event['exception'];
                }
            }
        }
        throw $event['exception'];
    }
}
