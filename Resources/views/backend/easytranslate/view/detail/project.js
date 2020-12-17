

Ext.define('Shopware.apps.Easytranslate.view.detail.Project', {
    extend: 'Shopware.model.Container',
    alias: 'widget.easytranslate-project-detail-container',
    padding: 20,

    configure: function () {
        var me = this;

        return {
            controller: 'Easytranslate',
            fieldSets: [{
                title: '{s name=fieldsetProjectDetailTitle}Project detail{/s}',
                layout: 'fit',
                fields: {
                    projectName: {
                        xtype: 'displayfield',
                        fieldLabel: '{s name=projectNameLabel}Project name{/s}',
                    },
                    objectType: {
                        xtype: 'displayfield',
                        fieldLabel: '{s name=objectTypeLabel}Translation object(s){/s}',
                    },
                    fieldsOfInterest: {
                        xtype: 'displayfield',
                        fieldLabel: '{s name=fieldsOfInterestLabel}Translation fields{/s}',
                        renderer: me.fieldsOfInterestRenderer
                    },
                    status: {
                        xtype: 'displayfield',
                        fieldLabel: '{s name=statusLabel}Status{/s}',
                    },
                    price: {
                        xtype: 'displayfield',
                        fieldLabel: '{s name=priceLabel}Price{/s}',
                        renderer: me.priceRenderer,
                    },
                }
            },
            Ext.bind(me.acceptDeclinePriceFieldSet, me),
            ],
            associations: ['tasks']
        };
    },

    acceptDeclinePriceFieldSet: function() {
        var me = this;

        const items = []
        if (!me.doesProjectNeedApproval()) {
            items.push( me.createInfoBox() );
        }
        items.push(me.createPriceAcceptButton(), me.createPriceDeclineButton())

        return Ext.create('Ext.form.FieldSet', {
            title: '{s name=fieldsetProjectPriceApprovalTitle}Project Price Approval{/s}',
            items: items
        })
    },

    doesProjectNeedApproval: function() {
        var me = this;
        return me.record.data.status === 'APPROVAL_NEEDED'
    },

    createInfoBox: function() {
        return Ext.create('Ext.container.Container', {
            html: '{s name=projectPriceApprovalInfo}The price for this project is already accepted or declined{/s}.',
            style: 'font-style: normal; color: #000; margin: 0 0 10px 5px; font-size: 12px;'
        });
    },

    createPriceAcceptButton: function() {
        var me = this;
        return Ext.create('Ext.button.Button', {
            text: '{s name=projectPriceAcceptButton}Accept price{/s}',
            hidden: false,
            disabled: !me.doesProjectNeedApproval(),
            action: 'my-action',
            cls: 'primary',
            padding: 4,
            handler: function () {
                Ext.Ajax.request({
                    url: '{url controller=Easytranslate action=acceptOrDeclineProjectPrice}',
                    method: 'POST',
                    params: {
                        projectId: me.record.data.projectId,
                        acceptOrDecline: 'accept'
                    },
                    success: function(operation, opts) {
                        try {
                            var response = Ext.decode(operation.responseText);
                        } catch (e) {
                            var response = null
                        }

                        if (!response || response.success === false) {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=projectPriceAcceptErrorTitle}Error{/s}',
                                '{s name=projectPriceAcceptErrorText}Could not accept price for project{/s}',
                            );
                        } else {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=projectPriceAcceptSuccessTitle}Success{/s}',
                                '{s name=projectPriceAcceptSuccessText}Project price successfully accepted{/s}',
                            );
                        }
                    }
                });
            }
        });
    },

    createPriceDeclineButton: function() {
        var me = this;
        return Ext.create('Ext.button.Button', {
            text: '{s name=projectPriceDeclineButton}Decline price{/s}',
            hidden: false,
            disabled: !me.doesProjectNeedApproval(),
            action: 'my-action',
            cls: 'primary',
            padding: 4,
            handler: function () {
                Ext.Ajax.request({
                    url: '{url controller=Easytranslate action=acceptOrDeclineProjectPrice}',
                    method: 'POST',
                    params: {
                        projectId: me.record.data.projectId,
                        acceptOrDecline: 'decline'
                    },
                    success: function(operation, opts) {
                        try {
                            var response = Ext.decode(operation.responseText);
                        } catch (e) {
                            var response = null
                        }

                        if (!response || response.success === false) {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=projectPriceDeclineErrorTitle}Error{/s}',
                                '{s name=projectPriceDeclineErrorText}Could not decline price for project{/s}',
                            );
                        } else {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=projectPriceDelineSuccessTitle}Success{/s}',
                                '{s name=projectPriceDeclineSuccessText}Project price successfully declined{/s}',
                            );
                        }
                    }
                });
            }
        });
    },

    fieldsOfInterestRenderer: function(value, record) {
        var me = this;
        return JSON.parse(value).join(', ');
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
    }
});
