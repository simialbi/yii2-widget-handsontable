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

	CKEditor.prototype.open = function () {
		$(this.TEXTAREA).ckeditor().editor.setData('<p>' + this.getValue() + '</p>');

		Handsontable.editors.TextEditor.prototype.open.call(this);
	};

	CKEditor.prototype.finishEditing = function (restoreOriginalValue, ctrlDown, callback) {
		var dialog = $('.cke_dialog');
		if (dialog.length && dialog.is(':visible')) {
			return;
		}

		Handsontable.editors.TextEditor.prototype.finishEditing.call(this, restoreOriginalValue, ctrlDown, callback);
	};

	CKEditor.prototype.close = function (tdOutside) {
		var value = this.getValue().trim(),
			stripped = value.replace(/(<([^>]+)>)/ig, '');
		if (value === '<p>' + stripped + '</p>') {
			this.setValue(stripped);
		}

		Handsontable.editors.TextEditor.prototype.close.call(this, tdOutside);
	};

	Handsontable.editors.registerEditor('rtfEditor', CKEditor);
})(Handsontable, jQuery, CKEDITOR);