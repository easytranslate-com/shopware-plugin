<?php

use Easytranslate\Models\TranslationProfile;

/**
 * Backend controllers extending from Shopware_Controllers_Backend_Application do support the new backend components
 */
class Shopware_Controllers_Backend_TranslationProfile extends Shopware_Controllers_Backend_Application
{
    protected $model = TranslationProfile::class;
    protected $alias = 'translationProfile';

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
}
