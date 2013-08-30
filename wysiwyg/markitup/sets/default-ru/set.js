// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
var mySettings = {
    resizeHandle: false,
	onShiftEnter:  	{keepDefault:false, replaceWith:'<br />\n'},
	onCtrlEnter:  	{keepDefault:false, openWith:'\n<p>', closeWith:'</p>'},
	onTab:    		{keepDefault:false, replaceWith:'    '},
	markupSet:  [
		{name:'Жирный', key:'B', openWith:'<b>', closeWith:'</b>', className: 'btnBold'},
		{name:'Наклонный', key:'I', openWith:'<i>', closeWith:'</i>', className: 'btnItalic'},
		{name:'Подчеркнутый', key:'U', openWith:'<u>', closeWith:'</u>', className: 'btnUnderline'},
		{name:'Зачеркнутый', key:'S', openWith:'<s>', closeWith:'</s>', className: 'btnStroke'},
		{name:'Список', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ul>\n', closeBlockWith:'\n</ul>', className: 'btnOl'},
		{name:'Нумерованный список', openWith:'    <li>', closeWith:'</li>', multiline:true, openBlockWith:'<ol>\n', closeBlockWith:'\n</ol>', className: 'btnUl'},
		{name:'Цитата', openWith:'<blockquote>[![Текст цитаты]!]', closeWith:'</blockquote>', className: 'btnQuote'},
        {name:'Ссылка', key:'L', openWith:'<a href="[![Адрес ссылки:!:http://]!]">', closeWith:'</a>', placeHolder:'Заголовок ссылки...', className: 'btnLink'},
		{name:'Фото из Интернета', replaceWith:'<img src="[![Адрес фото:!:http://]!]" alt="[![Описание]!]" />', className: 'btnImg'},
		{name:'Фото с компьютера', className: 'btnImgUpload', beforeInsert: function(markItUp) { InlineUpload.display(markItUp) }},
		{name:'Видео YouTube', openWith:'<youtube>[![Ссылка на ролик YouTube]!]', closeWith:'</youtube>', className: 'btnVideoYoutube'},
	]
}
