<?php
class formAdminCtypesBasic extends cmsForm {

    public function init($do) {

        return array(
            'titles' => array(
                'type' => 'fieldset',
                'childs' => array(
                    new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => array(
                            array('required'),
                            array('sysname'),
                            $do == 'add' ? array('unique', 'content_types', 'name') : false
                        )
                    )),
                    new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
                            array('max_length', 100)
                        )
                    )),
                    new fieldString('description', array(
                        'title' => LANG_DESCRIPTION,
                        'rules' => array(
                            array('max_length', 255)
                        )
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_PUBLICATION,
                'childs' => array(
                    new fieldCheckbox('is_premod_add', array(
                        'title' => LANG_CP_PREMOD_ADD
                    )),
                    new fieldCheckbox('is_premod_edit', array(
                        'title' => LANG_CP_PREMOD_EDIT
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_CATEGORIES,
                'childs' => array(
                    new fieldCheckbox('is_cats', array(
                        'title' => LANG_CP_CATEGORIES_ON
                    )),
                    new fieldCheckbox('is_cats_recursive', array(
                        'title' => LANG_CP_CATEGORIES_RECURSIVE
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_CT_GROUPS,
                'childs' => array(
                    new fieldCheckbox('is_in_groups', array(
                        'title' => LANG_CP_CT_GROUPS_ALLOW
                    )),
                    new fieldCheckbox('is_in_groups_only', array(
                        'title' => LANG_CP_CT_GROUPS_ALLOW_ONLY
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_COMMENTS,
                'childs' => array(
                    new fieldCheckbox('is_comments', array(
                        'title' => LANG_CP_COMMENTS_ON
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_RATING,
                'childs' => array(
                    new fieldCheckbox('is_rating', array(
                        'title' => LANG_CP_RATING_ON
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_TAGS,
                'childs' => array(
                    new fieldCheckbox('is_tags', array(
                        'title' => LANG_CP_TAGS_ON
                    )),
                    new fieldCheckbox('options:is_tags_in_list', array(
                        'title' => LANG_CP_TAGS_IN_LIST
                    )),
                    new fieldCheckbox('options:is_tags_in_item', array(
                        'title' => LANG_CP_TAGS_IN_ITEM
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_SEOMETA,
                'childs' => array(
                    new fieldCheckbox('is_auto_keys', array(
                        'title' => LANG_CP_SEOMETA_AUTO_KEYS
                    )),
                    new fieldCheckbox('is_auto_desc', array(
                        'title' => LANG_CP_SEOMETA_AUTO_DESC
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_URL_SETTINGS,
                'childs' => array(
                    new fieldCheckbox('is_auto_url', array(
                        'title' => LANG_CP_AUTO_URL
                    )),
                    new fieldCheckbox('is_fixed_url', array(
                        'title' => LANG_CP_FIXED_URL
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_LISTVIEW_OPTIONS,
                'childs' => array(
                    new fieldCheckbox('options:list_on', array(
                        'title' => LANG_CP_LISTVIEW_ON,
                        'default' => true
                    )),
                    new fieldCheckbox('options:profile_on', array(
                        'title' => LANG_CP_PROFILELIST_ON,
                        'default' => true
                    )),
                    new fieldCheckbox('options:list_show_filter', array(
                        'title' => LANG_CP_LISTVIEW_FILTER
                    )),
                )
            ),
            array(
                'type' => 'fieldset',
                'title' => LANG_CP_ITEMVIEW_OPTIONS,
                'childs' => array(
                    new fieldCheckbox('options:item_on', array(
                        'title' => LANG_CP_ITEMVIEW_ON,
                        'default' => true
                    )),
                )
            ),
        );

    }

}