
Ext.define('Shopware.apps.TranslationProfile', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.TranslationProfile',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.TranslationProfile',

        'detail.Window',
        'detail.TranslationProfile',
    ],

    models: [
        'TranslationProfile',
    ],
    stores: [
        'TranslationProfile',
    ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
