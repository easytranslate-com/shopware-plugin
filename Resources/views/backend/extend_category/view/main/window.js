//{block name="backend/category/view/main/window"}
// {$smarty.block.parent}

Ext.define('Shopware.apps.ExtendCategory.view.main.Window', {
    override: 'Shopware.apps.Category.view.main.Window',

    /**
     * Build and returns the action toolbar in the footer of the form.
     *
     * @public
     * @return Array of docked items
     */
    getDockedItems : function() {
        var me = this;

        var toolbar = me.callParent(arguments);

        /* {if {acl_is_allowed privilege=update}} */
        toolbar[0].items.unshift({
            text: 'Translate',
            iconCls: 'sprite-edit-language',
            cls: 'secondary',
            handler: function () {
                var record = me.formPanel.getForm().getRecord();

                if(!record) {
                    console.warn("Cannot translate: no category selected.");
                    return;
                }

                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.TranslationForm',
                    action: 'load',
                    params: {
                        records: [record],
                        idField: 'id',
                        objectType: 'category'
                    }
                });
            }
        });
        /* {/if} */

        return toolbar
    }
});
//{/block}
