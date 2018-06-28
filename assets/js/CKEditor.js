/* globals Handsontable,jQuery,CKEDITOR,window:false */
(function (Handsontable, $, CK) {
	"use strict";

	var CKEditor = Handsontable.editors.TextEditor.prototype.extend();

	CKEditor.prototype.createElements = function () {
		this.TEXTAREA = document.createElement('TEXTAREA');
		this.TEXTAREA.tabIndex = -1;

		this.textareaStyle = this.TEXTAREA.style;
		this.textareaStyle.width = 0;
		this.textareaStyle.height = 0;

		this.TEXTAREA_PARENT = document.createElement('DIV');
		$(this.TEXTAREA_PARENT).addClass('handsontableInputHolder');

		this.textareaParentStyle = this.TEXTAREA_PARENT.style;
		this.textareaParentStyle.zIndex = '-1';

		this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

		this.instance.rootElement.appendChild(this.TEXTAREA_PARENT);

		$(this.TEXTAREA).ckeditor({
			toolbarGroups: [
				{
					name: 'clipboard',
					groups: ['undo', 'selection', 'clipboard', 'doctools']
				},
				{
					name: 'basicstyles',
					groups: ['basicstyles', 'cleanup']
				},
				{
					name: 'links'
				}
			],
			removeButtons: 'Iframe,Image,Table',
			enterMode: CK.ENTER_BR,
			shiftEnterMode: CK.ENTER_P,
			on: {
				instanceReady: function () {
					this.dataProcessor.writer.selfClosingEnd = '>';
					this.dataProcessor.writer.setRules('br', {
						indent: false,
						breakBeforeOpen: false,
						breakAfterOpen: false,
						breakBeforeClose: false,
						breakAfterClose: false
					});
				}
			}
		});
	};

	CKEditor.prototype.setValue = function (newValue) {
		if (!newValue.match(/^<[^>]+>/ig)) {
			newValue = '<p>' + newValue + '</p>';
		}
		$(this.TEXTAREA).ckeditor().editor.setData(newValue);

		Handsontable.editors.TextEditor.prototype.setValue.call(this, newValue);
	};

	CKEditor.prototype.getValue = function () {
		return $(this.TEXTAREA).ckeditor().editor.getData().trim();
	};

	Handsontable.editors.registerEditor('rtfEditor', CKEditor);
})(Handsontable, jQuery, CKEDITOR);