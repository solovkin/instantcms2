<?php

class onTypographHtmlFilter extends cmsAction {

    public function run($text){

        $errors = null;

        $text = $this->getJevix()->parse($text, $errors);

        return $text;

    }

    private function getJevix(){

        cmsCore::loadLib('jevix.class');

        $jevix = new Jevix();

        // Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
        $jevix->cfgAllowTags(array(
            'p', 'br', 'span',
            'a', 'img',
            'b', 'i', 'u', 's', 'del', 'em', 'strong', 'sup', 'sub', 'hr', 'font',
            'ul', 'ol', 'li',
            'table', 'tr', 'td', 'th',
            'h1','h2','h3','h4','h5','h6',
            'pre', 'code', 'blockquote',
            'video', 'audio', 'youtube'
        ));

        // Устанавливаем коротие теги. (не имеющие закрывающего тега)
        $jevix->cfgSetTagShort(array(
            'br','img', 'hr'
        ));

        // Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
        $jevix->cfgSetTagPreformatted(array(
            'code','pre'
        ));

        // Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
        $jevix->cfgSetTagCutWithContent(array(
            'script', 'style', 'meta', 'iframe'
        ));

        // Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
        $jevix->cfgAllowTagParams('a', array('href', 'name' => '#text'));
        $jevix->cfgAllowTagParams('img', array('src', 'alt' => '#text', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int'));
        $jevix->cfgAllowTagParams('span', array('style'));

        // Устанавливаем параметры тегов являющиеся обязательными. Без них вырезает тег оставляя содержимое.
        $jevix->cfgSetTagParamsRequired('img', 'src');
        $jevix->cfgSetTagParamsRequired('a', 'href');

        // Устанавливаем теги которые может содержать тег контейнер
        $jevix->cfgSetTagChilds('ul',array('li'),false,true);
        $jevix->cfgSetTagChilds('ol',array('li'),false,true);
        $jevix->cfgSetTagChilds('table',array('tr'),false,true);
        $jevix->cfgSetTagChilds('table',array('th'),false,true);
        $jevix->cfgSetTagChilds('tr',array('td'),false,true);
        $jevix->cfgSetTagChilds('tr',array('th'),false,true);

        // Устанавливаем автозамену
        $jevix->cfgSetAutoReplace(array('+/-', '(c)', '(с)', '(r)', '(C)', '(С)', '(R)'), array('±', '©', '©', '®', '©', '©', '®'));

        // включаем режим замены переноса строк на тег <br/>
        $jevix->cfgSetAutoBrMode(true);

        // включаем режим автоматического определения ссылок
        $jevix->cfgSetAutoLinkMode(true);

        // Отключаем типографирование в определенном теге
        $jevix->cfgSetTagNoTypography('code','pre','youtube');

        // Ставим колбэк для youtube
        $jevix->cfgSetTagCallback('youtube', array($this, 'parseYouTubeVideo'));

        return $jevix;

    }

    public function parseYouTubeVideo($content){

        $video_id = $this->parseYouTubeVideoID(trim($content));

        if (!$video_id) { return false; }

        $code = '<iframe width="320" height="240" src="http://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';

        return $code;

    }

    private function parseYouTubeVideoID($url) {

        $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';
        preg_match($pattern, $url, $matches);
        return (isset($matches[1])) ? $matches[1] : false;
        
    }

}
