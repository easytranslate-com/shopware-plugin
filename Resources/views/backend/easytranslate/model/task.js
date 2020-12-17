
Ext.define('Shopware.apps.Easytranslate.model.Task', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            listing: 'Shopware.apps.Easytranslate.view.detail.Task'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'sourceShopId', type: 'int' },
        { name : 'targetShopId', type: 'int' },
        { name : 'status', type: 'string' },
        { name : 'price', type: 'string' },
        { name : 'creationDate', type: 'date', dateFormat: 'Y-m-d H:i:s' },
        { name : 'deadline', type: 'date', dateFormat: 'Y-m-d' },
    ],
});

