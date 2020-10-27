

Ext.define('Shopware.apps.Easytranslate.view.list.Project', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.easytranslate-project-listing-grid',
    region: 'center',

    configure: function() {
        var me = this;
        return {
            detailWindow: 'Shopware.apps.Easytranslate.view.detail.Window',
            columns: {
                projectName: {
                    header: '{s name=projetNameHeader}Project name{/s}',
                    flex: 2,
                },
                objectType: {
                    header: '{s name=objectTypeHeader}Translation object(s){/s}',
                },
                fieldsOfInterest: {
                    header: '{s name=fieldsOfInterestHeader}Translation fields{/s}',
                    renderer: me.fieldsOfInterestRenderer
                },
                status: { },
                price: {
                    header: '{s name=priceHeader}Price{/s}',
                    renderer: me.priceRenderer
                }
            },
            deleteButton: false,
            addButton: false,
            actionColumn: true,
            deleteColumn: false,
        };
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

    fieldsOfInterestRenderer: function(value, record) {
        var me = this;
        return JSON.parse(value).join(', ');
    },

    createToolbarItems: function() {
        var me = this,
            items = me.callParent(arguments);

        items = Ext.Array.insert(
            items,
            2,
            [ me.createToolbarButton() ]
        );

        return items;
    },

    createToolbarButton: function() {
        var me = this;
        return Ext.create('Ext.button.Button', {
            text: '{s name=fetchProjectsButtonText}Reload Projects{/s}',
            tooltip: '{s name=fetchProjectsButtonTooltip}Updates the data of all projects{/s}',
            handler: function () {
                Ext.Ajax.request({
                    url: '{url controller=Easytranslate action=fetchProjects}',
                    method: 'POST',
                    success: function(operation, opts) {
                        var response = Ext.decode(operation.responseText);

                        if (response.success === false) {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=fetchPojectsErrorTitle}Error{/s}',
                                '{s name=fetchPojectsErrorMessage}Something went wrong fetching the projects{/s}',
                            );
                        } else {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=fetchPojectsSuccessTitle}Success{/s}',
                                '{s name=fetchPojectsSuccessMessage}Project listing updated{/s}',
                            );
                            // trigger data reload
                            me.reloadData(me.store);

                            // reload listing
                            var current = me.store.currentPage;
                            if (me.fireEvent('beforechange', me, current) !== false) {
                                me.store.loadPage(current);
                            }

                        }
                    }
                });
            }
        });
    }
});
