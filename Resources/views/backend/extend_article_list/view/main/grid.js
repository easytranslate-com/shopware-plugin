
//{namespace name=backend/article_list/main}
//{block name="backend/article_list/view/main/grid"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.ExtendArticleList.view.main.Grid', {
    override: 'Shopware.apps.ArticleList.view.main.Grid',

    /**
     * Creates the grid toolbar
     *
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getToolbar: function () {
        var me = this;

        var toolbar = me.callParent(arguments);

        /*{if {acl_is_allowed resource=article privilege=save}}*/
        toolbar.add(toolbar.items.items.length - 2, {
            xtype: 'button',
            text: 'Translate',
            iconCls: 'sprite-edit-language',
            handler: function () {
                var selectionModel = me.getSelectionModel(),
                    records = selectionModel.getSelection();

                if(!records) {
                    console.warn("Cannot translate: no article selected.");
                    return;
                }

                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.TranslationForm',
                    action: 'load',
                    params: {
                        records: records,
                        idField: 'Article_id',
                        objectType: 'article'
                    }
                });
            }
        });
        /*{/if}*/

        return toolbar;
    },
});
//{/block}
