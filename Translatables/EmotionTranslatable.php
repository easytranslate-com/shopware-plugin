<?php

namespace Easytranslate\Translatables;

use Doctrine\ORM\AbstractQuery;
use Easytranslate\Components\Easytranslate\Translatable;
use Shopware\Components\Api\Resource\Translation;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Emotion\Emotion;
use Shopware\Models\Translation\Translation as TranslationModel;


class EmotionTranslatable implements Translatable
{
    protected $entityName;
    protected $objectType;
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
        $this->objectType = 'emotion';
        $this->entityName = 's_emotion';

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

        // get fallback in default language
        $objectTranslationFallback = $this->getTranslationFallback($identifier);
        foreach ($fieldsOfInterest as $field) {
            if ($field === 'emotionElements') continue;
            $translationContent[$field] = $objectTranslationFallback[$field];
        }

        // get translations from s_core_translations (in sourceShop language)
        if ($objectTranslation = $this->getTranslation($identifier, $sourceShopId)) {
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

        if (in_array('emotionElements', $fieldsOfInterest)) {
            $translationContent['emotionElements'] = $this->getElementsContent($identifier, $sourceShopId);
        }

        return $translationContent;
    }

    protected function getElementsContent($emotionId, $sourceShopId) {
        $translationContent = array();

        $sql = 'SELECT * FROM s_emotion_element AS el
                JOIN s_emotion_element_value AS elVal ON el.id = elVal.elementID
                JOIN s_library_component_field AS field ON elVal.fieldID = field.id
                WHERE el.emotionID = :emotionId AND field.translatable = 1';
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->bindParam('emotionId', $emotionId);
        $stmt->execute();
        $res = $stmt->fetchAll();

        foreach ($res as $row) {
            $elementId = $row['elementID'];
            $fieldId = $row['fieldID'];
            $fieldName = $row['name'];
            $fieldValue = $row['value'];

            $translationContent[$elementId][$fieldId][$fieldName] = $fieldValue;

            /**
             * @var \Shopware_Components_Translation $translationComponent
             */
            $read = $this->translationComponent->read($sourceShopId, 'emotionElement', $elementId, false);
            if ($read && $read[$fieldName]) {
                $translationContent[$elementId][$fieldId][$fieldName] = $read[$fieldName];
            }
        }

        return $translationContent;
    }

    /**
     * Helper function which returns a language translation for a single object type.
     */
    protected function getTranslation($identifier, $shopId, $resultMode = AbstractQuery::HYDRATE_ARRAY)
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
    protected function getTranslationFallback($identifier, $resultMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $emotionRepository = Shopware()->Models()->getRepository(Emotion::class);
        /** @var QueryBuilder $builder */
        $builder = $emotionRepository->createQueryBuilder('emotions');
        $builder->setFirstResult(0)
            ->setMaxResults(1)
            ->addFilter(['id' => $identifier]
            );

        return $builder->getQuery()->getOneOrNullResult($resultMode);
    }


    public function setContent($taskId, $identifier, $content, $fieldsOfInterest, $task)
    {
        $shopId = $task->getTargetLocale()->getHostLanguage();

        // filter only the shopping experiences names
        $emotionContent = array_filter($content, function($key) {
            return $key !== 'emotionElements';
        }, ARRAY_FILTER_USE_KEY);

        foreach($emotionContent as $key => $value) {
            if($value == null) {
                $content[$key] = '';
            }
        }

        $currentTranslation = $this->getTranslation($identifier, $shopId);
        $currentTranslation['data'] = $this->translationComponent->unFilterData(
            $this->objectType,
            $currentTranslation['data']
        );

        $mergedEmotionContent = array_merge($currentTranslation['data'], $emotionContent);

        $this->translationComponent->write($shopId, $this->objectType, $identifier, $mergedEmotionContent, false);

        // look for the nested emotionElements translations and store the translations.
        foreach ($content as $key => $element) {
            if ($key == 'emotionElements') {
                foreach ($element as $elementId => $elementContent) {
                    $data = array();
                    foreach ($elementContent as $fieldId => $fieldContent) {
                        if (!$fieldContent[key($fieldContent)]) {
                            $fieldContent[key($fieldContent)] = '';
                        }
                        $data[key($fieldContent)] = $fieldContent[key($fieldContent)];
                    }

                    $this->translationComponent->write($shopId, 'emotionElement', $elementId, $data, false);
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
