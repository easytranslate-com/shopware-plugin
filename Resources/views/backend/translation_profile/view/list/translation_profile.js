Ext.define('Shopware.apps.TranslationProfile.view.list.TranslationProfile', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.translation-profile-grid',
    region: 'center',
    configure: function() {
        var me = this;

        me.languagesStore = Ext.create('Shopware.store.ShopLanguage',
            { fields: ['name'], autoLoad: true }
        );

        setTimeout(function() {
            if (me.languageNamesLoaded) { return; }
            me.reloadData(me.store);
            me.store.loadPage(me.store.currentPage);
        }, 300);
        setTimeout(function() {
            if (me.languageNamesLoaded) { return; }
            me.reloadData(me.store);
            me.store.loadPage(me.store.currentPage);
        }, 1000);
        setTimeout(function() {
            if (me.languageNamesLoaded) { return; }
            me.reloadData(me.store);
            me.store.loadPage(me.store.currentPage);
        }, 3000);

        return {
            columns: {
                profileName: {
                    header: '{s name=profileNameLabel}Profile name{/s}',
                    flex: 1
                },
                sourceShopId: {
                    flex: 1,
                    align: 'left',
                    header: '{s name=sourceShopHeader}Source shop{/s}',
                    renderer: Ext.bind(me.shopRenderer, me)
                },
                targetShopIds: {
                    flex: 2,
                    align: 'left',
                    header: '{s name=targetShopsHeader}Target shops{/s}',
                    renderer: Ext.bind(me.targetShopsRenderer, me)
                }
            },
            detailWindow: 'Shopware.apps.TranslationProfile.view.detail.Window'
        };
    },

    shopRenderer: function(value, record) {
        var me = this;

        // in case no source shop is defined (id = 0) display nothing
        if (value == 0) {
            return ''
        }

        if (me.languagesStore.loading === false) {
            me.languageNamesLoaded = true; // bit hacky workaround
            try {
                return me.languagesStore.getById(value).data.name;
            } catch (e) {
                return value
            }
        }

        // as backup if the languagesStore could not be loaded return the numerical value.
        return value;
    },

    targetShopsRenderer: function (value, record) {
        var me = this;

        if (!value) { return ""}

        return JSON.parse(value).sort().map(shopId => me.shopRenderer(shopId)).join(', ');
    }
});
