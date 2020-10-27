
Ext.define('Shopware.apps.Easytranslate.view.detail.Window', {
    extend: 'Shopware.window.Detail',
    alias: 'widget.easytranslate-project-detail-window',

    title : '{s name=windowTitle}Project detail{/s}',
    autoHeight: true,
    width: 900,
    configure: function () {
        return { }
    },

    createDockedItems: function () {
        // we do not need the toolbar with "Save" and "Cancel" button on the detail page.
        return [];
    },
});
