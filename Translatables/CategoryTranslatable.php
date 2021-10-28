<?php

namespace Easytranslate\Translatables;

use Doctrine\ORM\AbstractQuery;
use Easytranslate\Components\Easytranslate\Translatable;
use Shopware\Components\Api\Resource\Translation;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Category\Category;
use Shopware\Models\Translation\Translation as TranslationModel;


class CategoryTranslatable implements Translatable
{
    protected $entityName;
    protected $objectType;
    protected $objectTypeAttributes;
    protected $objectId;
    protected $connection;
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
        $this->objectType = 'category';
        $this->objectTypeAttributes = 's_categories_attributes';
        $this->entityName = 's_categories';

        $this->container = Shopware()->Container();
        $this->connection = $this->container->get('dbal_connection');
        $this->em = $this->container->get('models');
        $this->translationComponent = $this->container->get('translation');
        $this->resource = $this->container->get('shopware.api.translation');
    }

    private function getMapping(): array {
        return [
            "description" => "name",
            "cmsheadline" => "cmsHeadline",
            "cmstext" => "cmsText",
            "metatitle" => "metaTitle",
            "metadescription" => "metaDescription",
            "metakeywords" => "metaKeywords",
            "attribute1" => "attribute1",
            "attribute2" => "attribute2",
            "attribute3" => "attribute3",
            "attribute4" => "attribute4",
            "attribute5" => "attribute5",
        ];
    }

    public function getContent($identifier, $fieldsOfInterest, $sourceLanguage)
    {
        $sourceShopId = $sourceLanguage->getHostLanguage();

        $mapping = $this->getMapping();

        $translationContent = array();

        // get fallback translations from entity itself (in default language)
        $objectTranslationFallback = $this->getObjectTranslationFallback($identifier);

        foreach ($fieldsOfInterest as $field) {
            $objectField = $mapping[$field];
            $translationContent[$field] = $objectTranslationFallback[$objectField];
        }

        // get translations from s_core_translations (in sourceShop language)
        if ($objectTranslation = $this->getObjectTranslation($identifier, $sourceShopId)) {
            $objectTranslation['data'] = $this->translationComponent->unFilterData(
                'category',
                $objectTranslation['data']
            );
            foreach ($fieldsOfInterest as $field) {
                if ($objectTranslation['data'][$field] !== NULL) {
                    $translationContent[$field] = $objectTranslation['data'][$field];
                }
            }
        };

        return $translationContent;
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

        $normalContent = [];
        $attributesContent = [];

        foreach ($mergedContent as $key => $value) {
            if (strstr($key, "attribute")) {
                $attributesContent[str_replace("attribute", "__attribute_attribute", $key)] = $value;
            } else {
                $normalContent[$key] = $value;
            }
        }

        $this->translationComponent->write($shopId, $this->objectType, $identifier, $normalContent, false);
        $this->translationComponent->write($shopId, $this->objectTypeAttributes, $identifier, $attributesContent, false);
    }


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
        $builder2 = $translationRepository->createQueryBuilder('translations');
        $builder2->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter([
                'key' => $identifier,
                'type' => $this->objectTypeAttributes,
                'shopId' => $shopId,
            ]);

        return array_merge($builder->getQuery()->getOneOrNullResult($resultMode), $builder2->getQuery()->getOneOrNullResult($resultMode));

    }

    protected function getObjectTranslationFallback($identifier, $resultMode = AbstractQuery::HYDRATE_ARRAY)
    {

        $categoryRepository = Shopware()->Models()->getRepository(Category::class);
        $categoryAttributeRepository = Shopware()->Models()->getRepository(\Shopware\Models\Attribute\Category::class);
        /** @var QueryBuilder $builder */
        $builder = $categoryRepository->createQueryBuilder('categories');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['id' => $identifier]
            );
        $builder2 = $categoryAttributeRepository->createQueryBuilder('categories_attributes');
        $builder2->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['categoryId' => $identifier]
            );

        return array_merge($builder->getQuery()->getOneOrNullResult($resultMode), $builder2->getQuery()->getOneOrNullResult($resultMode));

    }

    public function getType()
    {
        // Implement getType() method.
    }

    public function getId()
    {
        // Implement getId() method.
    }
}
