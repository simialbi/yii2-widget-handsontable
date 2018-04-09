/* globals Handsontable,jQuery,window:false */
(function (Handsontable, $) {
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
			removeButtons: 'Iframe,Image,Table'
		});
	};

	CKEditor.prototype.close = function () {
		$(this.TEXTAREA).ckeditor().editor.destroy();

		var value = this.getValue(),
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
})(Handsontable, jQuery);