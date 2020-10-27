
//{namespace name=backend/snippet/view/main}
//{block name="backend/snippet/view/main/grid"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.ExtendSnippet.view.main.Grid', {
    override: 'Shopware.apps.Snippet.view.main.Grid',
    extend: 'Ext.grid.Panel',
    alias: 'widget.snippet-main-grid',
    enableColumnHide: false,

    /**
     * Creates the grid toolbar
     *
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getToolbar: function() {
        var me = this;

        var toolbar = me.callParent(arguments);

        /*{if {acl_is_allowed resource=article privilege=save}}*/
        toolbar.add(toolbar.items.items.length - 3, {
            xtype: 'button',
            text: 'Translate',
            iconCls: 'sprite-edit-language',
            handler: function () {
                var records = me.selModel.getSelection();

                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.TranslationForm',
                    action: 'load',
                    params: {
                        records: records,
                        idField: function(record) {
                            return record.namespace + ':' + record.name;
                        },
                        objectType: 'snippet'
                    }
                });
            }
        });
        /*{/if}*/

        return toolbar;
    }
});
//{/block}
