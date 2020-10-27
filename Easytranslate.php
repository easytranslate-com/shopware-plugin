<?php

namespace Easytranslate;

use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Shopware-Plugin Easytranslate.
 */
class Easytranslate extends Plugin
{
    /**
     * Adds the widget to the database and creates the database schema.
     *
     * @param Plugin\Context\InstallContext $installContext
     */
    public function install(Plugin\Context\InstallContext $installContext)
    {
        parent::install($installContext);

        $this->createSchema();

    }

    /**
     * Remove widget and remove database schema.
     *
     * @param Plugin\Context\UninstallContext $uninstallContext
     */
    public function uninstall(Plugin\Context\UninstallContext $uninstallContext)
    {
        parent::uninstall($uninstallContext);

        $this->removeSchema();
    }

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('easytranslate.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
     * creates database tables on base of doctrine models
     */
    private function createSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Project::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\TaskLog::class)
        ];
        $tool->createSchema($classes);
    }

    private function removeSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\TaskLog::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Project::class)
        ];
        $tool->dropSchema($classes);
    }
}
