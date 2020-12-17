Ext.define('Shopware.apps.TranslationProfile.model.TranslationProfile', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'TranslationProfile',
            detail: 'Shopware.apps.TranslationProfile.view.detail.TranslationProfile'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'profileName', type: 'string' },
        { name : 'sourceShopId', type: 'int' },
        { name : 'targetShopIds', type: 'string' }
    ]
});
