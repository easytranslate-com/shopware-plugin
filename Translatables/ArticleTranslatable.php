<?php

namespace Easytranslate\Translatables;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Easytranslate\Components\Easytranslate\Translatable;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Article\Article;
use Shopware\Models\Translation\Translation as TranslationModel;
use Shopware\Components\Api\Resource\Translation;


class ArticleTranslatable implements Translatable
{
    protected $entityName;
    protected $objectType;
    protected $objectId;
    protected $connection;
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var \Shopware_Components_Translation
     */
    private $translationComponent;

    /**
     * @var Translation $resource
     */
    protected $resource;

    public function __construct()
    {
        $this->objectType = 'article';
        $this->entityName = 's_articles';

        $this->container = Shopware()->Container();
        $this->connection = $this->container->get('dbal_connection');
        $this->em = $this->container->get('models');
        $this->translationComponent = $this->container->get('translation');
        $this->resource = $this->container->get('shopware.api.translation');
    }

    public function getContent($identifier, $fieldsOfInterest, $sourceLanguage)
    {
        $sourceShopId = $sourceLanguage->getHostLanguage();

        $translationContent = array();

        // get fallback for name, description_long, description, metaTitle, keywords in default language
        $objectTranslationFallback = $this->getObjectTranslationFallback($identifier);
        foreach ($fieldsOfInterest as $field) {
            $translationContent[$field] = $objectTranslationFallback[$field];
        }

        // get translations from s_core_translations (in sourceShop language)
        if ($objectTranslation = $this->getObjectTranslation($identifier, $sourceShopId)) {
            $objectTranslation['data'] = $this->translationComponent->unFilterData(
                $this->objectType,
                $objectTranslation['data']
            );
            foreach ($fieldsOfInterest as $field) {
                if ($objectTranslation['data'][$field] !== NULL) {
                    $translationContent[$field] = $objectTranslation['data'][$field];
                }
            }
        }

        return $translationContent;
    }

    /**
     * Helper function which returns a language translation for a single object type.
     */
    protected function getObjectTranslation($identifier, $shopId, $resultMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $translationRepository = Shopware()->Models()->getRepository(TranslationModel::class);
        /** @var QueryBuilder $builder */
        $builder = $translationRepository->createQueryBuilder('translations');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter([
                'key' => $identifier,
                'type' => $this->objectType,
                'shopId' => $shopId,
            ]);

        return $builder->getQuery()->getOneOrNullResult($resultMode);
    }

    /**
     * Helper function which returns a language translation fallback for a single object type.
     */
    protected function getObjectTranslationFallback($identifier, $resultMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $articleRepository = Shopware()->Models()->getRepository(Article::class);
        /** @var QueryBuilder $builder */
        $builder = $articleRepository->createQueryBuilder('articles');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['id' => $identifier]
            );

        return $builder->getQuery()->getOneOrNullResult($resultMode);
    }

    public function getType()
    {
        return $this->objectType;
    }

    public function getId()
    {
        return $this->objectId;
    }

    public function setContent($taskId, $identifier, $content, $fieldsOfInterest, $task)
    {
        $shopId = $task->getTargetLocale()->getHostLanguage();

        foreach($content as $key => $value) {
            if($value == null) {
                $content[$key] = '';
            }
        }

        $currentTranslation = $this->getObjectTranslation($identifier, $shopId);
        $currentTranslation['data'] = $this->translationComponent->unFilterData(
            $this->objectType,
            $currentTranslation['data']
        );

        $mergedContent = array_merge($currentTranslation['data'], $content);

        $this->translationComponent->write($shopId, $this->objectType, $identifier, $mergedContent, false);
    }
}
