Ext.define('Shopware.apps.TranslationProfile.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.translation-profile-window',
    height: 450,
    width: 750,
    title : '{s name=window_title}Translation Profile{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.TranslationProfile.view.list.TranslationProfile',
            listingStore: 'Shopware.apps.TranslationProfile.store.TranslationProfile'
        };
    }
});
