
//{namespace name=backend/property/view/main}
//{block name="backend/Property/view/main/group_grid"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.ExtendProperty.view.main.GroupGrid', {
    override: 'Shopware.apps.Property.view.main.GroupGrid',

    /**
     * Sets up the ui component
     *
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.selModel = me.getGridSelModel();

        me.callParent(arguments);
    },

    /**
     * Creates the grid selection model for checkboxes
     *
     * @return [Ext.selection.CheckboxModel] grid selection model
     */
    getGridSelModel: function () {
        var me = this;

        return Ext.create('Ext.selection.CheckboxModel', {
            // allowDeselect: true, // better if true, but that interferes with the propertyvalues panel..
            listeners: {
                // Unlocks the delete button if the user has checked at least one checkbox
                selectionchange: function (sm, selections) {
                    // me.deleteButton.setDisabled(selections.length === 0);
                    // me.splitViewModeBtn.setDisabled(selections.length === 0);
                    // me.fireEvent('productchange', selections);
                }
            }
        });
    },

    /**
     * Creates the grid toolbar
     *
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getToolbar: function() {
        var me = this;

        var toolbar = me.callParent(arguments);

        /*{if {acl_is_allowed resource=article privilege=save}}*/
        toolbar.add(1, {
            xtype: 'button',
            text: 'Translate',
            iconCls: 'sprite-edit-language',
            handler: function () {
                var selectionModel = me.getSelectionModel(),
                    records = selectionModel.getSelection();

                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.TranslationForm',
                    action: 'load',
                    params: {
                        records: records,
                        idField: 'id',
                        objectType: 'propertyoption'
                    }
                });
            }
        });
        /*{/if}*/

        return toolbar;



    }
});
//{/block}
