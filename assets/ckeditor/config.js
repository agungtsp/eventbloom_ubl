/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    //config.baseFloatZIndex = 9000;
    //config.extraPlugins = 'wordcount';
    //config.wordcount = {
    //
    //    // Whether or not you want to show the Word Count
    //    showWordCount: true,
    //
    //    // Whether or not you want to show the Char Count
    //    showCharCount: false,
    //    wordLimit: 10
    //};
    config.extraPlugins = 'imagemanager,youtube';
config.toolbar  =
                    [
                       ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                       ['Bold','Italic','Underline'],
                       ['Link','Unlink','Imagemanager'],
                       [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
					   ['Subscript','Superscript'],
                       ['NumberedList','BulletedList', 'Outdent', 'Indent'],
					   ['Blockquote','-','BidiLtr', 'BidiRtl'],
                       ['Youtube','Preview', 'Print','Source'],
					   ['Table','HorizontalRule','Smiley','SpecialChar'],
					   ['TextColor','BGColor'],
					   [ 'find', 'selection', 'spellchecker' ]
                        
                        //,'/',
                        //['Source','Preview','Templates','Cut','Copy','Paste'],
                        //['Bold','Italic','Underline','Strike','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','NumberedList','BulletedList','Subscript','Superscript','-'],
                        //['Link','Unlink','-','Image','Flash','Table','HorizontalRule','Smiley','SpecialChar'],['TextColor','BGColor','-','Font','FontSize']
                    ];

     config.allowedContent = true;
config.extraAllowedContent = 'a[!href,document-href]';
config.autoUpdateElement = true;

};
