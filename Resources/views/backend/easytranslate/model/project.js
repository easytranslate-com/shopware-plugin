
Ext.define('Shopware.apps.Easytranslate.model.Project', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'Easytranslate',
            detail: 'Shopware.apps.Easytranslate.view.detail.Project'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'projectId', type: 'string'},
        { name : 'projectName', type: 'string'},
        { name : 'objectType', type: 'string' },
        { name : 'fieldsOfInterest', type: 'string'},
        { name : 'status', type: 'string' },
        { name : 'price', type: 'string' }
    ],

    associations: [
        {
            relation: 'OneToMany',
            storeClass: 'Shopware.apps.Easytranslate.store.Task',
            loadOnDemand: false,

            type: 'hasMany',
            model: 'Shopware.apps.Easytranslate.model.Task',
            name: 'getTasks',
            associationKey: 'tasks'
        },
    ]
});

