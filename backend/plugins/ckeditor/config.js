/**
 * Smart Hospital — Global CKEditor Configuration
 * Applied automatically to every CKEDITOR.replace() call that does not
 * specify its own customConfig.  Keep this toolbar minimal and universal.
 */
CKEDITOR.editorConfig = function( config ) {

    config.bodyClass = (document.body && document.body.classList.contains('variant-b')) ? 'variant-b' : '';

    config.allowedContent      = true;
    config.extraAllowedContent = 'p(*)[*]{*};div(*)[*]{*};li(*)[*]{*};ul(*)[*]{*}';
    config.enterMode           = CKEDITOR.ENTER_BR;
    config.shiftEnterMode      = CKEDITOR.ENTER_P;
    config.autoParagraph       = false;
    config.removePlugins       = 'elementspath';   // hides the "body > p" status bar at bottom

    config.toolbar = 'SHCompact';
    config.toolbar_SHCompact = [
        { name: 'document',    items: [ 'Source', 'Maximize' ] },
        { name: 'history',     items: [ 'Undo', 'Redo' ] },
        { name: 'styles',      items: [ 'Format' ] },
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat' ] },
        { name: 'lists',       items: [ 'BulletedList', 'NumberedList', 'Outdent', 'Indent' ] },
        { name: 'links',       items: [ 'Link', 'Unlink' ] },
        { name: 'insert',      items: [ 'Image', 'Table' ] },
        { name: 'align',       items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull' ] },
        { name: 'size',        items: [ 'FontSize' ] },
        { name: 'colors',      items: [ 'TextColor', 'BGColor' ] }
    ];

};
