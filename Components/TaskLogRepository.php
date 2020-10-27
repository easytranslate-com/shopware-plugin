<?php

namespace Easytranslate\Components;

use Easytranslate\Components\Easytranslate\Models\TaskLog;
use Easytranslate\Models\TaskLog as ShopwareTaskLog;
use Easytranslate\Components\Easytranslate\Repository;

/**
 * Class TaskLogRepository
 * @package Easytranslate\Components
 */
class TaskLogRepository implements Repository
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @param $em
     * @param $entity
     * @return ShopwareTaskLog
     */
    public static function toShopware($em, $entity): ShopwareTaskLog
    {
        return new ShopwareTaskLog(
            TaskRepository::toShopware($em, $entity->getTask()),
            $entity->getContent()
        );
    }

    /**
     * @param $em
     * @param $taskLog
     * @return TaskLog
     */
    public static function fromShopware($em, $taskLog): TaskLog
    {
        return new TaskLog(
            TaskRepository::fromShopware($em, $taskLog->getTranslationTask()),
            $taskLog->getContent()
        );
    }

    /**
     * @param $entity
     */
    function save($entity)
    {
        $this->em->persist(self::toShopware($this->em, $entity));
        $this->em->flush();
    }

    /**
     * @param $id
     * @return TaskLog
     */
    function load($id)
    {
        $translationTaskLog = $this->em->find(ShopwareTaskLog::class, $id);

        return self::fromShopware($this->em, $translationTaskLog);
    }

    function update($entity)
    {
        // not needed
    }
}
