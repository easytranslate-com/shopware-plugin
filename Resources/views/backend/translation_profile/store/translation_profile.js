Ext.define('Shopware.apps.TranslationProfile.store.TranslationProfile', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'TranslationProfile'
        };
    },
    model: 'Shopware.apps.TranslationProfile.model.TranslationProfile'
});
