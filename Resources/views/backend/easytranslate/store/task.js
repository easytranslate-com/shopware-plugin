
Ext.define('Shopware.apps.Easytranslate.store.Task', {
    extend:'Shopware.store.Association',

    configure: function() {
        return {
            controller: 'Easytranslate',
        };
    },

    model: 'Shopware.apps.Easytranslate.model.Task',
});
