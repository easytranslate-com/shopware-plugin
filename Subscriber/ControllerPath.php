<?php

namespace Easytranslate\Subscriber;

use Enlight\Event\SubscriberInterface;
use Easytranslate\Components\Easytranslate\Config;
use Easytranslate\Components\EasytranslateImpl;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ControllerPath implements SubscriberInterface
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
        return array(
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_Easytranslate' => 'onGetEasytranslateController',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_TranslationForm' => 'onGetTranslationFormController',
            'Enlight_Controller_Dispatcher_ControllerPath_Backend_TranslationProfile' => 'onGetTranslationProfileController'
        );
    }

    /**
     * Register the task controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_Task     */
    public function onGetEasytranslateController(\Enlight_Event_EventArgs $args)
    {
        Config::initialize(new EasytranslateImpl());
        $this->container->get('template')->addTemplateDir(__DIR__ . '/../Resources/views/');
        return __DIR__ . '/../Controllers/Backend/Easytranslate.php';
    }

    /**
     * Register the task controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_Task     */
    public function onGetTranslationFormController(\Enlight_Event_EventArgs $args)
    {
        Config::initialize(new EasytranslateImpl());
        $this->container->get('template')->addTemplateDir(__DIR__ . '/../Resources/views/');
        return __DIR__ . '/../Controllers/Backend/TranslationForm.php';
    }

    /**
     * Register the task controller
     *
     * @param   \Enlight_Event_EventArgs $args
     * @return  string
     * @Enlight\Event Enlight_Controller_Dispatcher_ControllerPath_Backend_Task     */
    public function onGetTranslationProfileController(\Enlight_Event_EventArgs $args)
    {
        Config::initialize(new EasytranslateImpl());
        $this->container->get('template')->addTemplateDir(__DIR__ . '/../Resources/views/');
        return __DIR__ . '/../Controllers/Backend/TranslationProfile.php';
    }
}
