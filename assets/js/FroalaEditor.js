/* globals Handsontable,jQuery,window:false */
(function (Handsontable, $) {
    "use strict";

    var FroalaEditor = Handsontable.editors.TextEditor.prototype.extend();

    FroalaEditor.prototype.createElements = function () {
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

        $(this.TEXTAREA).froalaEditor({
            toolbarInline: true,
            toolbarButtons: ['undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'clearFormatting', '|', 'insertLink']
        });
    };

    FroalaEditor.prototype.setValue = function (newValue) {
        if (!newValue.match(/^<[^>]+>/ig)) {
            newValue = '<p>' + newValue + '</p>';
        }
        $(this.TEXTAREA).froalaEditor('html.set', newValue);

        Handsontable.editors.TextEditor.prototype.setValue.call(this, newValue);
    };

    FroalaEditor.prototype.getValue = function () {
        return $(this.TEXTAREA).froalaEditor('html.get').trim();
    };

    Handsontable.editors.registerEditor('rtfEditor', FroalaEditor);
})(Handsontable, jQuery);