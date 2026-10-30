/**
 * Smart Hospital — CKEditor config for Front CMS page builder.
 * Loaded explicitly by admin/front/pages/create.php and edit.php via customConfig.
 * Uses the same SHCompact toolbar as the global config, plus Font Awesome support.
 */
CKEDITOR.editorConfig = function( config ) {

    config.allowedContent      = true;
    config.extraAllowedContent = 'p(*)[*]{*};div(*)[*]{*};li(*)[*]{*};ul(*)[*]{*}';
    config.enterMode           = CKEDITOR.ENTER_BR;
    config.shiftEnterMode      = CKEDITOR.ENTER_P;
    config.autoParagraph       = false;
    config.removePlugins       = 'elementspath';

    // Font Awesome icon support for page builder
    config.contentsCss = baseurl + '/backend/plugins/ckeditor/plugins/font-awesome/css/font-awesome.css';
    CKEDITOR.dtd.$removeEmpty['i']    = false;
    CKEDITOR.dtd.$removeEmpty.i       = 0;
    CKEDITOR.dtd.$removeEmpty['span'] = false;
    config.protectedSource.push( /<i class="fa[s|r|l|b] [A-Za-z0-9\-]+"><\/i>/g );

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
