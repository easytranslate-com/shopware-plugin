
Ext.define('Shopware.apps.TranslationForm', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.TranslationForm',

    loadPath: '{url action=load}',
    // bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        // 'form.Window'
    ],

    models: [],
    stores: [],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
