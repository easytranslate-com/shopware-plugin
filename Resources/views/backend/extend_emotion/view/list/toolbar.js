
//{namespace name=backend/emotion/list/toolbar}
//{block name="backend/emotion/list/toolbar"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.ExtendEmotion.view.list.Toolbar', {
    override: 'Shopware.apps.Emotion.view.list.Toolbar',

    initComponent: function() {
        var me = this;

        me.callParent(arguments);

        var toolbar = me;

        /*{if {acl_is_allowed resource=article privilege=save}}*/
        toolbar.add(toolbar.items.length - 3, {
            xtype: 'button',
            text: 'Translate',
            iconCls: 'sprite-edit-language',
            handler: function () {
                var selectionModel = me.ownerCt.ownerCt.ownerCt.gridPanel.selModel,
                    records = selectionModel.getSelection();

                if(!records) {
                    console.warn("Cannot translate: no shopping world selected.");
                    return;
                }

                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.TranslationForm',
                    action: 'load',
                    params: {
                        records: records,
                        idField: 'id',
                        objectType: 'emotion'
                    }
                });
            }
        });
        /*{/if}*/
    },
});
//{/block}
