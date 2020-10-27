<?php

namespace Easytranslate\Components;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Easytranslate\Components\Easytranslate\Models\Project;
use Easytranslate\Components\Easytranslate\Repository;
use Easytranslate\Models\Project as ShopwareProject;
use Shopware\Components\Model\QueryBuilder;

/**
 * Class ProjectRepository
 * @package Easytranslate\Components
 */
class ProjectRepository implements Repository
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * ProjectRepository constructor.
     * @param $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * @param $project ShopwareProject
     * @return Project
     */
    public static function fromShopware($project): Project {
        return new Project(
            $project->getProjectId(),
            $project->getProjectName(),
            $project->getObjectType(),
            $project->getFieldsOfInterest(),
            $project->getCallbackToken(),
            $project->getStatus(),
            $project->getPrice()
        );
    }

    /**
     * @param $em
     * @param $entity
     * @return ShopwareProject
     * @throws NonUniqueResultException
     */
    public static function toShopware($em, $entity): ShopwareProject {
        /** @var QueryBuilder $builder */
        $builder = $em->getRepository(ShopwareProject::class)->createQueryBuilder('project')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['projectId' => $entity->getProjectId()]
            );

        $project = $builder->getQuery()->getOneOrNullResult();

        if($project != null) {
            $project->setProjectName($entity->getProjectName());
            $project->setObjectType($entity->getObjectType());
            $project->setFieldsOfInterest($entity->getFieldsOfInterest());
            $project->setCallbackToken($entity->getCallbackToken());
            $project->setStatus($entity->getStatus());
            $project->setPrice($entity->getPrice());
            return $project;
        } else {
            return new ShopwareProject(
                $entity->getProjectId(),
                $entity->getProjectName(),
                $entity->getObjectType(),
                $entity->getFieldsOfInterest(),
                $entity->getCallbackToken(),
                $entity->getStatus(),
                $entity->getPrice()
            );
        }
    }

    /**
     * @param $entity Project
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    function save($entity)
    {
        $this->em->persist(self::toShopware($this->em, $entity));
        $this->em->flush();
    }

    /**
     * @param $id
     * @return Project
     * @throws NonUniqueResultException
     */
    function load($id)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->em->getRepository(ShopwareProject::class)->createQueryBuilder('project')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['projectId' => $id]
            );

        $translationProject = $builder->getQuery()->getOneOrNullResult();
        return self::fromShopware($translationProject);

    }

    /**
     * @param $entity
     */
    function update($entity)
    {
        $this->save($entity);
    }
}
