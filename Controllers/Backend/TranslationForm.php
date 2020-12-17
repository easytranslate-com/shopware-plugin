<?php

use Easytranslate\Components\Easytranslate\Language;
use Easytranslate\Components\Easytranslate\Translator;
use Easytranslate\Components\EasytranslateMapping;
use Easytranslate\Models\Project;
use Shopware\Models\Shop\Shop;

/**
 * Backend controllers extending from Shopware_Controllers_Backend_Application do support the new backend components
 */
class Shopware_Controllers_Backend_TranslationForm extends Shopware_Controllers_Backend_Application
{
    protected $model = Project::class;
    protected $alias = 'project';

    public function init() {
        parent::init();

        $this->setManager(Shopware()->Models());
    }

    protected function getListQuery()
    {
        $builder = parent::getListQuery();

        return $builder;
    }

    protected function getDetailQuery($id)
    {
        return parent::getDetailQuery($id);
    }

    protected function getAdditionalDetailData(array $data)
    {
        $data['tasks'] = array();
        return $data;
    }

    public function startTranslationAction() {
        $objectType = $this->Request()->getParam('objectType');
        $identifiers = $this->Request()->getParam('identifiers');
        $sourceShopId = $this->Request()->getParam('sourceShop');
        $targetShopIds = $this->Request()->getParam('targetShops');
        $fieldsOfInterest = $this->Request()->getParam('fieldsOfInterest');
        $projectName = $this->Request()->getParam('projectName');

        $shopRepository = $this->manager->getRepository(Shop::class);

        try {
            $sourceShop = $shopRepository->find($sourceShopId);
            $targetShops = $shopRepository->findBy(['id' => $targetShopIds]);

            $sourceLanguage = new Language(
                EasytranslateMapping::getSourceLocaleFromShop($this->manager, $sourceShop),
                $sourceShopId
            );

            $targetLanguages = array_map(function($shop) {
                $locale = EasytranslateMapping::getTargetLocaleFromShop($this->manager, $shop);
                return new Language($locale, $shop->getId());
            }, $targetShops);
        } catch (Exception $e) {
            $this->View()->assign([
                'success' => false,
                'data' => 'Could not find selected languages. Please check your input.',
            ]);
            return;
        }

        try {
            $project = Translator::translate($identifiers, $objectType, $fieldsOfInterest, $sourceLanguage,
                $targetLanguages, $projectName);
        } catch (Exception $e) {
            $this->View()->assign([
                'success' => false,
                'data' => 'Internal error during project request',
            ]);
            return;
        }

        $this->View()->assign([
            'success' => true,
            'data' => $identifiers,
        ]);
    }
}
