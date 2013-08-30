<?php

class formWidgetUsersListOptions extends cmsForm {

    public function init() {

        cmsCore::loadControllerLanguage('users');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:dataset', array(
                        'title' => LANG_WD_USERS_LIST_DATASET,
                        'items' => array(
                            'latest' => LANG_USERS_DS_LATEST,
                            'rating' => LANG_USERS_DS_RATED,
                            'popular' => LANG_USERS_DS_POPULAR,
                        )
                    )),

                    new fieldCheckbox('options:is_avatars', array(
                        'title' => LANG_WD_USERS_LIST_AVATARS
                    )),

                    new fieldListGroups('options:groups', array(
                        'title' => LANG_WD_USERS_LIST_GROUPS,
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => array(
                            array('required')
                        )
                    )),

                )
            ),

        );

    }

}
