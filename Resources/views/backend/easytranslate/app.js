
Ext.define('Shopware.apps.Easytranslate', {
    extend: 'Enlight.app.SubApplication',

    name:'Shopware.apps.Easytranslate',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'list.Window',
        'list.Project',

        'detail.Window',
        'detail.Project',
        'detail.Task'
    ],

    models: [
        'Project',
        'Task'
    ],
    stores: [
        'Project',
        'Task'
    ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
