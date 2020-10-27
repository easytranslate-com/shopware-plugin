<?php

namespace Easytranslate\Subscriber;

use Enlight\Event\SubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExtendProperty implements SubscriberInterface
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
        return array('Enlight_Controller_Action_PostDispatchSecure_Backend_Property' => 'onPropertyPostDispatch');
    }

    /**
     * Register the backend controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_Easytranslate     */
    public function onPropertyPostDispatch(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Backend_Property $controller */
        $controller = $args->getSubject();

        $view = $controller->View();
        $request = $controller->Request();

        $this->container->get('template')->addTemplateDir(__DIR__ . '/../Resources/views/');

        if ($request->getActionName() == 'load') {
            $view->extendsTemplate('backend/extend_property/view/main/group_grid.js');
        }
    }

}
