<?php

namespace Easytranslate\Translatables;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Easytranslate\Components\Easytranslate\Translatable;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Property\Option;
use Shopware\Models\Property\Value;
use Shopware\Models\Translation\Translation as TranslationModel;
use Shopware\Components\Api\Resource\Translation;


class PropertyOptionTranslatable implements Translatable
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
     * @var array
     */
    private $translationContent;

    /**
     * @var Translation $resource
     */
    protected $resource;

    public function __construct()
    {
        $this->objectType = 'propertyoption';
        $this->entityName = 's_filter_options';

        $this->container = Shopware()->Container();
        $this->connection = $this->container->get('dbal_connection');
        $this->em = $this->container->get('models');
        $this->translationComponent = $this->container->get('translation');
        $this->resource = $this->container->get('shopware.api.translation');
    }

    private function getMapping(): array {
        return [
            "optionGroupName" => "name",
            "optionValueName" => "value"
        ];
    }


    public function getContent($identifier, $fieldsOfInterest, $sourceLanguage)
    {
        $sourceShopId = $sourceLanguage->getHostLanguage();

        $mapping = $this->getMapping();

        $translationContent = array();

        if (in_array('optionGroupName', $fieldsOfInterest)) {
            // get fallback translations from entity itself (in default language)
            $translationContent['optionName'] = $this->getGroupTranslationFallback($identifier)[$mapping['optionGroupName']];
            // get translations from s_core_translations (in sourceShop language)
            if ($objectTranslation = $this->getGroupTranslation($identifier, $sourceShopId)) {
                $objectTranslation['data'] = $this->translationComponent->unFilterData(
                    'propertyoption',
                    $objectTranslation['data']
                );
                if ($objectTranslation['data']['optionName'] !== NULL) {
                    $translationContent['optionName'] = $objectTranslation['data']['optionName'];
                }
            };
        }

        if (in_array('optionValueName', $fieldsOfInterest)) {
            $translationContent['optionValueName'] = $this->getValueContent($identifier, $sourceShopId);
        }

        return $translationContent;
    }

    /**
     * Helper function which returns a language translation for a single object type.
     */
    protected function getGroupTranslation($identifier, $shopId, $resultMode = AbstractQuery::HYDRATE_ARRAY)
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
    protected function getGroupTranslationFallback($identifier, $resultMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $filterOptionsRepository = Shopware()->Models()->getRepository(Option::class);
        /** @var QueryBuilder $builder */
        $builder = $filterOptionsRepository->createQueryBuilder('options');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['id' => $identifier]
            );

        return $builder->getQuery()->getOneOrNullResult($resultMode);
    }

    protected function getValueContent($groupIdentifier, $sourceShopId) {
        $translationContent = array();

        $filterValueRepository = Shopware()->Models()->getRepository(Value::class);
        /** @var QueryBuilder $builder */
        $builder = $filterValueRepository->createQueryBuilder('values');
        $builder->setFirstResult(0)
            ->addFilter(['option' => $groupIdentifier]);
        $res = $builder->getQuery()->getArrayResult();

        foreach ($res as $value) {
            // fallback of value in default language
            $translationContent[$value['id']]['optionValue'] = $value['value'];

            // overwrite if there is a translation in sourceShop language
            if ($objectTranslation = $this->getValueTranslation($value['id'], $sourceShopId)) {
                $objectTranslation['data'] = $this->translationComponent->unFilterData(
                    'propertyvalue',
                    $objectTranslation['data']
                );
                if ($objectTranslation['data']['optionValue'] !== NULL) {
                    $translationContent[$value['id']]['optionValue'] = $objectTranslation['data']['optionValue'];
                }
            }
        }

        return $translationContent;
    }

    /**
     * Helper function which returns a language translation for a single object type.
     */
    protected function getValueTranslation($identifier, $sourceShopId, $resultMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $translationRepository = Shopware()->Models()->getRepository(TranslationModel::class);
        /** @var QueryBuilder $builder */
        $builder = $translationRepository->createQueryBuilder('translations');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter([
                'key' => $identifier,
                'type' => 'propertyvalue',
                'shopId' => $sourceShopId,
            ]);

        return $builder->getQuery()->getOneOrNullResult($resultMode);
    }

    public function setContent($taskId, $identifier, $content, $fieldsOfInterest, $task)
    {
        $shopId = $task->getTargetLocale()->getHostLanguage();

        // we need to handle propertygroup and the nested propertyvalue seperately

        // filter only the group names
        $groupContent = array_filter($content, function($key) {
           return $key == 'optionName';
        }, ARRAY_FILTER_USE_KEY);

        foreach($groupContent as $key => $value) {
            if($value == null) {
                $content[$key] = '';
            }
        }

        $this->translationComponent->write($shopId, $this->objectType, $identifier, $groupContent, false);

        // look for the nested value translations and store the translations.
        foreach ($content as $key => $value) {
            if ($key == 'optionValueName') {
                foreach ($value as $valueIdentifier => $valueContent) {
                    if ($valueContent['optionValue'] == null) {
                        $valueContent['optionValue'] = '';
                    }
                    $this->translationComponent->write($shopId, 'propertyvalue', $valueIdentifier, $valueContent, false);
                }
            }
        }
    }

    public function getType()
    {
        return $this->objectType;
    }

    public function getId()
    {
        return $this->objectId;
    }
}
