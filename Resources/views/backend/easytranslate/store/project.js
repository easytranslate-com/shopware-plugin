
Ext.define('Shopware.apps.Easytranslate.store.Project', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'Easytranslate',
        };
    },

    model: 'Shopware.apps.Easytranslate.model.Project'
});
