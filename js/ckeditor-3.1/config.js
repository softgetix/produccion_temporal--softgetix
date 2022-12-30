/*

Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/


CKEDITOR.config.toolbar_Medium = [
	['Bold', 'Italic','Underline', 'Strike','Subscript','Superscript','-', 'NumberedList', 'BulletedList', '-','PasteText','PasteFromWord','RemoveFormat','-','Link','Unlink'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	['Styles','Format','Font','FontSize'],
	['Image','Flash','Preview']
];


CKEDITOR.config.toolbar_Colbasic = [
	//['Bold', 'Italic','Underline', '-', 'NumberedList', 'BulletedList', '-','PasteText','PasteFromWord','RemoveFormat']
	['Bold', 'Italic','Underline', '-', 'NumberedList', 'BulletedList']
];

CKEDITOR.addStylesSet( 'default', [{
    name: 'Título',
    element: 'h4'/*,
    attributes: { // acá se definen los atributos
		'class': 'my_style'
	},
    styles: { // acá se definen los estilos
    	'background-color': 'Yellow'
    }*/
}, {
    name: 'Subtítulo',
    element: 'h5'
}]);


/*CKEDITOR.editorConfig = function( config ){
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	//config.width = '524px';
	//config.toolbar = 'Medium';
};*/


