<?php

namespace Easytranslate\Subscriber;

use Enlight\Event\SubscriberInterface;
use Easytranslate\Components\Easytranslate\Config;
use Easytranslate\Components\EasytranslateImpl;

class WebhookSubscriber implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'Webhook_Event' => 'webhookCalled'
        ];
    }

    public function webhookCalled(\Enlight_Event_EventArgs $args)
    {
        Config::initialize(new EasytranslateImpl());
    }
}
