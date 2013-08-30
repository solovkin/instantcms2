<?php
class formAdminContentCategory extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'childs' => array(

                    new fieldList('parent_id', array(
                        'title' => LANG_PARENT_CATEGORY,
                        'generator' => function($cat){

                            $content_model = cmsCore::getModel('content');
                            $tree = $content_model->getCategoriesTree($cat['ctype_name']);

                            if ($tree){
                                foreach($tree as $item){

                                    // при редактировании исключаем себя и вложенные
                                    // подкатегории из списка выбора родителя
                                    if (isset($cat['ns_left'])){
                                        if ($item['ns_left'] >= $cat['ns_left'] && $item['ns_right'] <= $cat['ns_right']){
                                            continue;
                                        }
                                    }

                                    $items[$item['id']] = str_repeat('- ', $item['ns_level']).' '.$item['title'];

                                }
                            }

                            return $items;

                        }
                    )),

                    new fieldText('title', array(
                        'title' => LANG_CP_CONTENT_CATS_TITLES,
                        'hint' => LANG_CP_CONTENT_CATS_TITLES_HINT,
                        'rules' => array(
                            array('required'),
                        )
                    )),

                )
            )


        );

    }

}
