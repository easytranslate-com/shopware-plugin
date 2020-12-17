Ext.define('Shopware.apps.TranslationProfile.view.detail.TranslationProfile', {
    extend: 'Shopware.model.Container',
    padding: 20,

    configure: function() {
        return {
            controller: 'TranslationProfile',
            fieldSets: [{
                title: null,
                layout: 'anchor',
                fields: {
                    profileName: {
                        fieldLabel: '{s name=profileNameLabel}Profile name{/s}'
                    },
                    sourceShopId: this.getSourceLanguageComboBox,
                    targetShopIds: this.getTargetLanguageComboBox,
                }
            }]
        };
    },

    getSourceLanguageComboBox: function (model, formField) {
        var me = this;

        var languagesStore = Ext.create('Shopware.store.ShopLanguage',
            { fields: ['name'], autoLoad: true }
        );

        var comboBox = Ext.create('Ext.form.ComboBox', {
            fieldLabel: '{s name=sourceLanguageLabel}Source Language{/s}',
            store: languagesStore,
            queryMode: 'remote',
            anyMatch: true,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            triggerAction: 'all',
            helpText: '{s name=sourceLanguageHelpText}The language of the selected shop will be used as the source language{/s}',
            listeners:{
                'select':function() {
                    // update record
                    me.record.data.sourceShopId = this.value
                },
                'afterrender': function () {
                    // some manual dom manipulation for layouting
                    let id = this.bodyEl.id

                    setTimeout(function() {
                        document.querySelector('#' + id).parentElement.children[0].children[0].style.width = '130px';
                        document.querySelector('#' + id).style.width = '230px';
                        document.querySelector('#' + id).children[0].style.width = '100%';
                    },10)
                }
            }
        });

        // set initial value
        comboBox.value = model.data.sourceShopId;

        return comboBox
    },

    getTargetLanguageComboBox: function (model, formField) {
        var me = this;

        var languagesStore = Ext.create('Shopware.store.ShopLanguage',
            { fields: ['name'], autoLoad: true }
        );

        var comboBox = Ext.create('Ext.form.ComboBox', {
            fieldLabel: '{s name=targetLanguagesLabel}Target Languages{/s}',
            store: languagesStore,
            queryMode: 'remote',
            anyMatch: true,
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiSelect: true,
            helpText: '{s name=targetLanguageHelpText}The content will be translated in to the languages of the selected shops{/s}',
            listeners:{
                'select':function() {
                    // update record
                    me.record.data.targetShopIds = JSON.stringify(this.value)
                },
                'afterrender': function () {
                    // some manual dom manipulation for layouting
                    let id = this.bodyEl.id

                    setTimeout(function() {
                        document.querySelector('#' + id).parentElement.children[0].children[0].style.width = '130px';
                        document.querySelector('#' + id).style.width = '230px';
                        document.querySelector('#' + id).children[0].style.width = '100%';
                    },10)
                }
            }
        });

        // set initial value
        if (model.data.targetShopIds) {
            comboBox.value = JSON.parse(model.data.targetShopIds);
        }

        return comboBox
    }

});
