Ext.define('Shopware.apps.TranslationForm.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function () {
        var me = this;

        me.objectType = me.subApplication.params.objectType;
        me.records = me.subApplication.params.records;
        me.idField = me.subApplication.params.idField;

        me.mainWindow = Ext.create('Ext.window.Window', {
            title: '{s name=windowTitle}New Translation{/s}',
            autoHeight: true,
            width: 500,
            layout: 'fit',
            items: [
                me.getForm()
            ]
        }).show();
    },

    getForm: function () {
        var me = this;
        return Ext.create('Ext.form.Panel', {
            labelWidth: 75, // label settings here cascade unless overridden
            frame: false,
            bodyStyle: 'padding:5px 5px 0',
            autoHeight: true,
            width: 550,
            renderTo: Ext.getBody(),
            layout: 'column', // arrange fieldsets side by side
            defaults: {
                bodyPadding: 4
            },
            items: [{
                xtype: 'fieldset',
                columnWidth: 1,
                title: '{s name=windowTitle}New Translation{/s}',
                defaultType: 'textfield',
                defaults: { anchor: '100%' },
                layout: 'anchor',
                items: [
                    me.getProjectNameField(),
                ]
            },{
                xtype: 'fieldset',
                columnWidth: 1,
                title: '{s name=fieldSets/languages}Languages{/s}',
                defaultType: 'textfield',
                defaults: { anchor: '100%' },
                layout: 'anchor',
                items: [
                    me.getSourceLanguageComboBox(),
                    me.getTargetLanguageComboBox(),
                ]
            },
                {
                    xtype: 'fieldset',
                    columnWidth: 1,
                    title: '{s name=fieldSets/translationContent}Translation Content{/s}',
                    defaultType: 'textfield',
                    defaults: { anchor: '100%' },
                    layout: 'anchor',
                    items: [
                        me.getFieldsOfInterestComboBox(),
                    ]
                },
                {
                    xtype: 'container',
                    columnWidth: 1,
                    defaults: { anchor: '100%' },
                    layout: {
                        type: 'hbox',
                        align: 'middle',
                        pack: 'center',
                    },
                    items: [
                        me.getTranslateButton(),
                    ]
                },
            ],
        });
    },

    getProjectNameField: function () {
        var me = this;

        var defaultValue =
            'SW5 - translation of ' + me.records.length + ' ' + me.objectType;

        var textField = Ext.create('Ext.form.field.Text', {
            fieldLabel: '{s name=projectNameLabel}Project Name{/s}',
            allowBlank: false,
            maxLength: 255,
            helpText: '{s name=projectNameHelpText}The name of the generated project{/s}',
            value: defaultValue,
            style: 'padding-left: 10px',
        });

        me.projectNameField = textField;

        return textField
    },

    getSourceLanguageComboBox: function () {
        var me = this;

        var languagesStore = Ext.create('Shopware.store.ShopLanguage',
            { fields: ['name'], autoLoad: true }
        );

        var comboBox = Ext.create('Ext.form.ComboBox', {
            fieldLabel: '{s name=sourceLanguageLabel}Source Language{/s}',
            store: languagesStore,
            queryMode: 'local',
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            helpText: '{s name=sourceLanguageHelpText}The language of the selected shop will be used as the source language{/s}',
            renderTo: Ext.getBody()
        });

        me.sourceShopComboBox = comboBox;

        return comboBox;
    },

    getTargetLanguageComboBox: function () {
        var me = this;
        var languagesStore = Ext.create('Shopware.store.ShopLanguage',
            { fields: ['name'], autoLoad: true }
        );

        var comboBox = Ext.create('Ext.form.ComboBox', {
            fieldLabel: '{s name=targetLanguageLabel}Target Language{/s}',
            store: languagesStore,
            queryMode: 'local',
            displayField: 'name',
            valueField: 'id',
            forceSelection: true,
            multiSelect: true,
            helpText: '{s name=targetLanguageHelpText}The content will be translated in to the languages of the selected shops{/s}',
            renderTo: Ext.getBody()
        });

        me.targetShopComboBox = comboBox;

        return comboBox;
    },

    getFieldsOfInterestComboBox: function () {
        var me = this;

        var fieldsStore = Ext.create('Ext.data.Store', {
            fields: ['technicalName', 'humanReadable'],
            data: this.getFieldsOfInterest().map(function(field) {
                return { technicalName: field['technicalName'], humanReadable: field['humanReadable'] }
            })
        });

        var comboBox =  Ext.create('Ext.form.ComboBox', {
            fieldLabel: '{s name=fieldsOfInterestLabel}Content{/s}',
            store: fieldsStore,
            queryMode: 'local',
            displayField: 'humanReadable',
            valueField: 'technicalName',
            forceSelection: true,
            multiSelect: true,
            helpText: '{s name=fieldsOfInterestHelpText}Select all the fields of which will be translated{/s}',
            renderTo: Ext.getBody()
        });

        me.fieldsOfInterestComboBox = comboBox;

        return comboBox;
    },

    getTranslateButton: function () {
        var me = this;
        return Ext.create('Ext.button.Button', {
            text: '{s name=translateButtonText}Start Translation{/s}',
            scale: 'medium',
            maxWidth: 200,
            cls: 'primary',
            style: 'margin: 20px 0 20px 0;',
            tooltip: '{s name=translateButtonTooltip}Starts the translation process{/s}',
            handler: function () {
                var data = {
                    projectName: me.projectNameField.value,
                    objectType: me.objectType,
                    identifiers: me.getIdsFromRecords(),
                    sourceShop: me.sourceShopComboBox.value,
                    targetShops: me.targetShopComboBox.value,
                    fieldsOfInterest: me.fieldsOfInterestComboBox.value,
                }

                Ext.Ajax.request({
                    url: '{url controller=TranslationForm action=startTranslation}',
                    method: 'POST',
                    jsonData: data,
                    success: function(operation, opts) {
                        var response = Ext.decode(operation.responseText);

                        if (response.success === false) {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=startTranslationErrorTitle}Error{/s}',
                                '{s name=startTranslationErrorMessage}Something went wrong creating the project.{/s} ' + response.error_message + '');
                        } else {
                            Shopware.Notification.createGrowlMessage(
                                '{s name=startTranslationSuccessTitle}Success{/s}',
                                '{s name=startTranslationSuccessMessage}New project successfully created{/s}');
                        }
                    }
                });
            }
        })
    },

    getFieldsOfInterest: function () {
        var me = this;
        return {
            "article": [
                { "technicalName": "name", "humanReadable": "Name"},
                { "technicalName": "descriptionLong", "humanReadable": "Long description"},
                { "technicalName": "description", "humanReadable": "Description"},
                { "technicalName": "metaTitle", "humanReadable": "Meta title"},
                { "technicalName": "keywords", "humanReadable": "Keywords"},
                // { "technicalName": "attr1", "humanReadable": "Freetext 1"},
                // { "technicalName": "attr2", "humanReadable": "Freetext 2"},
                // { "technicalName": "attr3", "humanReadable": "Comment"},
                // { "technicalName": "description_clear", "humanReadable": "description_clear"},
                // { "technicalName": "shippingtime", "humanReadable": "Delivery time"},
                // { "technicalName": "attr4", "humanReadable": "Attribute 4"},
                // { "technicalName": "attr5", "humanReadable": "Attribute 5"},
            ],
            "category": [
                { "technicalName": "description", "humanReadable": "Description"},
                { "technicalName": "cmsheadline", "humanReadable": "Header"},
                { "technicalName": "cmstext", "humanReadable": "Category description"},
                { "technicalName": "metatitle", "humanReadable": "Meta title"},
                { "technicalName": "metadescription", "humanReadable": "Meta description"},
                { "technicalName": "metakeywords", "humanReadable": "Meta keywords"},
            ],
            "snippet": [
                { "technicalName": "value", "humanReadable": "Snippet value"},
            ],
            "propertyoption": [
                { "technicalName": "optionGroupName", "humanReadable": "Name of group(s)"}, // name of group (stored in 's_filter_options')
                { "technicalName": "optionValueName", "humanReadable": "Names of all values in this group"}, // name of values assigned to group (stored in 's_filter_values')
            ],
            "emotion": [
                { "technicalName": "name", "humanReadable": "Name of emotion(s)"}, // name of shopping experience
                { "technicalName": "seoTitle", "humanReadable": "SEO title"},
                { "technicalName": "seoDescription", "humanReadable": "SEO description"},
                { "technicalName": "seoKeywords", "humanReadable": "SEO keywords"},
                { "technicalName": "emotionElements", "humanReadable": "Every translatable field of the emotion elements"}, // literally everything translatable from the emotion elements
            ]
        }[ me.objectType ];
    },

    getId(record) {
        var me = this;
        if(typeof me.idField === "function") {
            return me.idField(record);
        }
        return record[me.idField];
    },

    getIdsFromRecords: function () {
        var me = this;

        return me.records.map(function(record) {
            return me.getId(record.data);
        })
    },

});
