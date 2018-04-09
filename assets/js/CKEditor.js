/* globals Handsontable,jQuery,CKEDITOR,window:false */
(function (Handsontable, $, CK) {
	"use strict";

	var CKEditor = Handsontable.editors.TextEditor.prototype.extend();

	CKEditor.prototype.getValue = function () {
		return $(this.TEXTAREA).val();
	};

	CKEditor.prototype.setValue = function (newValue) {
		$(this.TEXTAREA).val(newValue);
	};

	CKEditor.prototype.open = function () {
		this.refreshDimensions();

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

	CKEditor.prototype.close = function () {
		$(this.TEXTAREA).ckeditor().editor.destroy();

		var value = this.getValue().trim(),
			stripped = value.replace(/(<([^>]+)>)/ig, '');
		if (value === '<p>' + stripped + '</p>') {
			this.setValue(stripped);
		}

		this.textareaParentStyle.display = 'none';

		this.autoResize.unObserve();

		if (document.activeElement === this.TEXTAREA) {
			this.instance.listen(); // don't refocus the table if user focused some cell outside of HT on purpose
		}
	};

	Handsontable.editors.registerEditor('rtfEditor', CKEditor);
})(Handsontable, jQuery, CKEDITOR);