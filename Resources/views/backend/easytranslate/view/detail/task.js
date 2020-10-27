
Ext.define('Shopware.apps.Easytranslate.view.detail.Task', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.easytranslate-task-grid',
    title: '{s name=taskDetailTitle}Tasks{/s}',
    height: 300,

    configure: function() {
        var me = this;

        me.languagesStore = Ext.create('Shopware.store.ShopLanguage',
            { fields: ['name'], autoLoad: true }
        );

        setTimeout(function() {
            if (me.languageNamesLoaded) { return; }
            me.reloadData(me.store);
            me.store.loadPage(me.store.currentPage);
        }, 1000);

        return {
            // detailWindow: 'Shopware.apps.Easytranslate.view.detail.Window',
            columns: {
                sourceShopId: {
                    flex: 1,
                    header: '{s name=sourceShopHeader}Source shop{/s}',
                    renderer: Ext.bind(me.shopRenderer, me)
                },
                targetShopId: {
                    flex: 1,
                    header: '{s name=targetShopHeader}Target shop{/s}',
                    renderer: Ext.bind(me.shopRenderer, me)
                },
                status: {
                    flex: 2,
                    header: '{s name=StatusHeader}Status{/s}',
                },
                price: {
                    header: '{s name=PriceHeader}Price{/s}',
                    renderer: me.priceRenderer,
                    flex: 2
                }
            },
            deleteButton: false,
            addButton: false,
            actionColumn: true,
            deleteColumn: false,
            editColumn: false,
            searchField: false,
            toolbar: true,
            pagingbar: false
        };
    },

    createToolbarItems: function() {
        var me = this,
            items = me.callParent(arguments);

        items = Ext.Array.insert(
            items,
            0,
            [ me.createToolbarButton() ]
        );

        return items;
    },

    createToolbarButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: '{s name=fetchTranslationButtonText}Fetch translation{/s}',
            cls: 'secondary',
            style: 'padding: 5px 10px 5px 10px; margin-top: 5px; margin-bottom: 5px',
            handler: function () {

                var selectionModel = me.getSelectionModel(),
                    records = selectionModel.getSelection();

                if (records.length === 0) {
                    Ext.MessageBox.alert(
                        '',
                        '{s name=fetchTranslationNoTaskSelectedMessage}You have to select atleast 1 task{/s}',
                        function () {})
                    return;
                }

                Ext.MessageBox.confirm(
                    '{s name=fetchTranslationConfirmationTitle}Confirmation{/s}',
                    '{s name=fetchTranslationConfirmationText}You are about to fetch the translations of the selected task(s). These translations will be written into the database and overwrite existing translations. \nContinue?,\n{/s}',
                    function (btn) {
                        if (btn === 'yes') {
                            var data = {
                                taskId: me.getTaskIdsFromRecords(records),
                            }

                            Ext.Ajax.request({
                                url: '{url controller=Easytranslate action=fetchTranslatedContent}',
                                method: 'POST',
                                jsonData: data,
                                success: function(operation, opts) {
                                    var response = Ext.decode(operation.responseText);

                                    if (response.success === false) {
                                        Shopware.Notification.createGrowlMessage(
                                            '{s name=fetchTranslationErrorTitle}Error{/s}',
                                            '{s name=fetchTranslationErrorMessage}Something went wrong fetching the translation(s){/s}',
                                        );
                                    } else {
                                        Shopware.Notification.createGrowlMessage(
                                            '{s name=fetchTranslationSuccessTitle}Success{/s}',
                                            '{s name=fetchTranslationSuccessMessage}Translations updated{/s}',
                                        );
                                    }
                                }
                            });
                        }
                    }
                );
            }
        });
    },

    shopRenderer: function(value, record) {
        var me = this;

        if (me.languagesStore.loading === false) {
            me.languageNamesLoaded = true; // bit hacky workaround
            return me.languagesStore.getById(value).data.name;
        }
        // as backup if the languagesStore could not be loaded return the numerical value.
        return value;
    },

    priceRenderer: function (value, record) {
        var me = this;
        const price = JSON.parse(value);
        if (!price.total) {
            return 'N/A';
        }
        var total = (price.total / 100).toLocaleString(undefined, { maximumFractionDigits: 2, minimumFractionDigits: 2 });
        var totalEuro = (price.total_euro / 100).toLocaleString(undefined, { maximumFractionDigits: 2, minimumFractionDigits: 2 });
        return total + " " + price.currency + " (" + totalEuro + " EUR)";
    },

    getTaskIdsFromRecords: function (records) {
        var me = this;
        return records.map(function(record) {
            return record.raw.taskId;
        })
    },
});
