<?php
class cmsWysiwyg{

	function __construct(){

	}

	public function displayEditor($field_id, $content=''){

        cmsTemplate::getInstance()->addJS('wysiwyg/imperavi/editor.js');
        cmsTemplate::getInstance()->addCSS('wysiwyg/imperavi/css/editor.css');

        $dom_id = str_replace(array('[',']'), array('_', ''), $field_id);

        echo html_textarea($field_id, $content, array('id'=>$dom_id));

        ?>

            <script type="text/javascript">
                $(document).ready(function(){
                    $('#<?php echo $dom_id; ?>').editor({ toolbar: 'original', focus:false, height:'200px' });
                });
            </script>

        <?php

	}

}