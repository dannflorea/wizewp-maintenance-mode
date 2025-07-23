jQuery(function ($) {
    const cssEditorField = $('textarea[name="wizemamo_settings[custom_css]"]');
    if (!cssEditorField.length) return;

    const editor = wp.codeEditor.initialize(cssEditorField, {
        codemirror: {
            mode: 'css',
            lineNumbers: true,
            indentUnit: 2,
            tabSize: 2
        }
    });
});