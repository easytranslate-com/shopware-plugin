<?php

namespace Easytranslate\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExtendEmotion implements SubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array('Enlight_Controller_Action_PostDispatchSecure_Backend_Emotion' => 'onEmotionPostDispatch');
    }

    /**
     * Register the backend controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_Easytranslate     */
    public function onEmotionPostDispatch(\Enlight_Event_EventArgs $args)
    {


        /** @var \Shopware_Controllers_Backend_Emotion $controller */
        $controller = $args->getSubject();

        $view = $controller->View();
        $request = $controller->Request();

        $this->container->get('template')->addTemplateDir(__DIR__ . '/../Resources/views/');

        if ($request->getActionName() == 'load') {
            $view->extendsTemplate('backend/extend_emotion/view/list/toolbar.js');
        }
    }

}
