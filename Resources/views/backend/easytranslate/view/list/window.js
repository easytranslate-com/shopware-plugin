
Ext.define('Shopware.apps.Easytranslate.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.easytranslate-list-window',
    autoHeight: true,
    width: 900,
    title : '{s name=windowTitle}Project listing{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.Easytranslate.view.list.Project',
            listingStore: 'Shopware.apps.Easytranslate.store.Project'
        };
    },
});
