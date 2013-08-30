<?php
function routes_content(){

    return array(

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/add\/([0-9]+)$/i',
            'action'    => 'item_add',
            1           => 'ctype_name',
            2           => 'to_id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/add$/i',
            'action'    => 'item_add',
            1           => 'ctype_name'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/edit\/([0-9]+)$/i',
            'action'    => 'item_edit',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/approve\/([0-9]+)$/i',
            'action'    => 'item_approve',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/delete\/([0-9]+)$/i',
            'action'    => 'item_delete',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/addcat\/([0-9]+)$/i',
            'action'    => 'category_add',
            1           => 'ctype_name',
            2           => 'to_id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/addcat$/i',
            'action'    => 'category_add',
            1           => 'ctype_name',
            'to_id'     => 0
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/editcat\/([0-9]+)$/i',
            'action'    => 'category_edit',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(

            'pattern'   => '/^([a-z0-9\-]+)\/delcat\/([0-9]+)$/i',
            'action'    => 'category_delete',
            1           => 'ctype_name',
            2           => 'id'
        ),

        array(
            'pattern'   => '/^([a-z0-9\-]+)\/([a-zA-Z0-9\-]+).html$/i',
            'action'    => 'item_view',
            1           => 'ctype_name',
            2           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9]+)\-([a-z0-9]+)\/([a-zA-Z0-9\-\/]+)$/i',
            'action'    => 'item_category',
            1           => 'ctype_name',
            2           => 'dataset',
            3           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9]+)\/([a-zA-Z0-9\-\/]+)$/i',
            'action'    => 'item_category',
            1           => 'ctype_name',
            2           => 'slug'
        ),

        array(
            'pattern'   => '/^([a-z0-9]+)\-([a-z0-9]+)$/i',
            'action'    => 'item_category',
            1           => 'ctype_name',
            2           => 'dataset',
            'slug'      => 'index'
        ),

        array(
            'pattern'   => '/^([a-z0-9]+)$/i',
            'action'    => 'item_category',
            1           => 'ctype_name',
            'slug'      => 'index'
        ),

    );

}
