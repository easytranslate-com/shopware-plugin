<?php

namespace Easytranslate\Components;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Easytranslate\Components\Easytranslate\Language;
use Easytranslate\Components\Easytranslate\Models\Task;
use Easytranslate\Components\Easytranslate\Repository;
use Easytranslate\Models\Task as ShopwareTask;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Shop\Shop;

/**
 * Class TaskRepository
 * @package Easytranslate\Components
 */
class TaskRepository implements Repository
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @param $em EntityManager
     * @param $entity Task
     * @return ShopwareTask
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public static function toShopware($em, $entity): ShopwareTask {

        /** @var QueryBuilder $builder */
        $builder = $em->getRepository(ShopwareTask::class)->createQueryBuilder('task')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['taskId' => $entity->getTaskId()]
            );

        /** @var ShopwareTask $task */
        $task = $builder->getQuery()->getOneOrNullResult();

        /** @var Shop $sourceShop */
        $sourceShop = $em->find(Shop::class, $entity->getSourceLocale()->getHostLanguage());
        /** @var Shop $targetShop */
        $targetShop = $em->find(Shop::class, $entity->getTargetLocale()->getHostLanguage());

        if ($task != null) {
            $task->setTranslationProject(ProjectRepository::toShopware($em, $entity->getProject()));
            $task->setStatus($entity->getStatus());
            $task->setPrice($entity->getPrice());
            return $task;
        } else {
            return new ShopwareTask(
                $entity->getTaskId(),
                $sourceShop,
                $targetShop,
                ProjectRepository::toShopware($em, $entity->getProject()),
                $entity->getStatus(),
                $entity->getPrice()
            );
        }
    }

    /**
     * @param $em EntityManager
     * @param $task ShopwareTask
     * @return Task
     */
    public static function fromShopware($em, $task): Task {
        $sourceLocale = EasytranslateMapping::getSourceLocaleFromShop($em, $task->getSourceShop());
        $targetLocale = EasytranslateMapping::getTargetLocaleFromShop($em, $task->getTargetShop());

        return new Task(
            $task->getTaskId(),
            new Language($sourceLocale, $task->getSourceShop()->getId()),
            new Language($targetLocale, $task->getTargetShop()->getId()),
            ProjectRepository::fromShopware($task->getTranslationProject()),
            $task->getStatus(),
            $task->getPrice()
        );
    }

    /**
     * @param $entity Task
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    function save($entity)
    {
        $this->em->persist(self::toShopware($this->em, $entity));
        $this->em->flush();
    }

    function load($id)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->em->getRepository(ShopwareTask::class)->createQueryBuilder('task')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['taskId' => $id]
            );

        $translationTask = $builder->getQuery()->getOneOrNullResult();
        return self::fromShopware($this->em, $translationTask);
    }

    function update($entity)
    {
        $this->save($entity);
    }
}
