<?php

class modelContent extends cmsModel{

//============================================================================//
//===================    ПРЕФИКС ТАБЛИЦ КОНТЕНТА   ===========================//
//============================================================================//

    public $table_prefix = 'con_';

    protected $approved_filter_disabled = false;
    protected $approved_filtered = false;

    public function setTablePrefix($prefix){
        $this->table_prefix = $prefix;
        return $this;
    }

//============================================================================//
//=======================    ТИПЫ КОНТЕНТА   =================================//
//============================================================================//

    public function addContentType($ctype){

        $id = $this->insert('content_types', $ctype);

        // получаем структуру таблиц для хранения контента данного типа
        $content_table_struct = $this->getContentTableStruct();
        $fields_table_struct = $this->getFieldsTableStruct();

        // создаем таблицы
        $table_name = $this->table_prefix . $ctype['name'];

        $this->db->createTable($table_name, $content_table_struct);
        $this->db->createTable("{$table_name}_fields", $fields_table_struct, 'InnoDB');
        $this->db->createCategoriesTable("{$table_name}_cats");

        //
        // добавляем стандартные поля
        //

        // заголовок
        $this->addContentField($ctype['name'], array(
            'name' => 'title',
            'title' => LANG_TITLE,
            'type' => 'caption',
            'ctype_id' => $id,
            'is_in_list' => 1,
            'is_in_item' => 1,
            'is_in_filter' => 1,
            'is_fixed' => 1,
            'is_fixed_type' => 1,
            'is_system' => 0,
            'options' => array(
                'label_in_list' => 'none',
                'label_in_item' => 'none',
                'min_length' => 3,
                'max_length' => 100,
                'is_required' => true
            )
        ), true);

        // дата публикации
        $this->addContentField($ctype['name'], array(
            'name' => 'date_pub',
            'title' => LANG_DATE_PUB,
            'type' => 'date',
            'ctype_id' => $id,
            'is_in_list' => 1,
            'is_in_item' => 1,
            'is_in_filter' => 1,
            'is_fixed' => 1,
            'is_fixed_type' => 1,
            'is_system' => 1,
            'options' => array(
                'label_in_list' => 'none',
                'label_in_item' => 'left',
                'show_time' => true
            )
        ), true);

        // автор
        $this->addContentField($ctype['name'], array(
            'name' => 'user',
            'title' => LANG_AUTHOR,
            'type' => 'user',
            'ctype_id' => $id,
            'is_in_list' => 1,
            'is_in_item' => 1,
            'is_in_filter' => 0,
            'is_fixed' => 1,
            'is_fixed_type' => 1,
            'is_system' => 1,
            'options' => array(
                'label_in_list' => 'none',
                'label_in_item' => 'left'
            )
        ), true);

        // описание
        $this->addContentField($ctype['name'], array(
            'name' => 'content',
            'title' => LANG_DESCRIPTION,
            'type' => 'text',
            'ctype_id' => $id,
            'is_in_list' => 1,
            'is_in_item' => 1,
            'is_fixed' => 1,
            'options' => array(
                'label_in_list' => 'none',
                'label_in_item' => 'none'
            )
        ), true);

        cmsCache::getInstance()->clean("content.types");

        return $id;

    }

//============================================================================//
//============================================================================//

    public function updateContentType($id, $item){

        cmsCache::getInstance()->clean("content.types");

        return $this->update('content_types', $id, $item);

    }

//============================================================================//
//============================================================================//

    public function deleteContentType($id){

        $item = $this->getContentType($id);

        $this->delete('content_types', $id);
        $this->delete('content_datasets', $id, 'ctype_id');

        $table_name = $this->table_prefix . $item['name'];

        $this->db->dropTable("{$table_name}");
        $this->db->dropTable("{$table_name}_fields");
        $this->db->dropTable("{$table_name}_cats");

        cmsCache::getInstance()->clean("content.types");

        return true;

    }

//============================================================================//
//============================================================================//

    public function getContentTypesCount(){

        return $this->getCount('content_types');

    }

//============================================================================//
//============================================================================//

    public function getContentTypes(){

        $this->useCache('content.types');

        return $this->get('content_types', function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['labels'] = cmsModel::yamlToArray($item['labels']);

            return $item;

        });

    }

    public function getContentTypesNames(){

        return $this->get('content_types', function($item, $model){

            return $item['name'];

        }, false);

    }

//============================================================================//
//============================================================================//

    public function getContentType($id, $by_field='id'){

        $this->useCache('content.types');

        return $this->getItemByField('content_types', $by_field, $id, function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['labels'] = cmsModel::yamlToArray($item['labels']);

            return $item;

        });

    }

    public function getContentTypeByName($name){
        return $this->getContentType($name, 'name');
    }

//============================================================================//
//=======================    ПОЛЯ КОНТЕНТА   =================================//
//============================================================================//

    public function getDefaultContentFieldOptions(){

        return array(
            'is_required' => 0,
            'is_digits' => 0,
            'is_number' => 0,
            'is_alphanumeric' => 0,
            'is_email' => 0,
            'is_unique' => 0,
            'label_pos' => 'left'
        );

    }

//============================================================================//
//============================================================================//

    public function addContentField($ctype_name, $field, $is_virtual=false){

        $content_table_name = $this->table_prefix . $ctype_name;
        $fields_table_name = $this->table_prefix . $ctype_name . '_fields';

        $field['ordering'] = $this->getNextOrdering($fields_table_name);

        if (!$is_virtual){

            $field_class = "field" . string_to_camel('_', $field['type']);
            $field_parser = new $field_class(null, null);

            $sql = "ALTER TABLE {#}{$content_table_name} ADD `{$field['name']}` {$field_parser->getSQL()}";
            $this->db->query($sql);

            if ($field['is_in_filter'] && $parser->allow_index){
                $sql = "ALTER TABLE `{#}{$content_table_name}` ADD INDEX ( `{$field['name']}` )";
                $this->db->query($sql);
            }

        }

        $id = $this->insert($fields_table_name, $field);

        return $id;

    }

//============================================================================//
//============================================================================//

    public function getContentFieldsCount($ctype_name){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        return $this->getCount($table_name);

    }

//============================================================================//
//============================================================================//

    public function getContentFields($ctype_name){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->ctype_name = $ctype_name;

        $this->orderBy('ordering');

        cmsForm::loadFormFields();

        return $this->get($table_name, function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);
            $item['options'] = array_merge($model->getDefaultContentFieldOptions(), $item['options']);
            $item['groups_read'] = cmsModel::yamlToArray($item['groups_read']);
            $item['groups_edit'] = cmsModel::yamlToArray($item['groups_edit']);
            $item['default'] = $item['values'];

            $fields_types = cmsForm::getAvailableFormFields(false);
            $field_class = "field" . string_to_camel('_', $item['type']);

            $rules = array();
            if ($item['options']['is_required']) {  $rules[] = array('required'); }
            if ($item['options']['is_digits']) {  $rules[] = array('digits'); }
            if ($item['options']['is_number']) {  $rules[] = array('number'); }
            if ($item['options']['is_alphanumeric']) {  $rules[] = array('alphanumeric'); }
            if ($item['options']['is_email']) {  $rules[] = array('email'); }
            if ($item['options']['is_unique']) {  $rules[] = array('unique', $model->ctype_name, $item['name']); }

            $item['rules'] = $rules;

            $item['handler_title'] = $fields_types[$item['type']];
            $item['handler'] = new $field_class($item['name'], $item);

            return $item;

        }, 'name');

    }

    public function getRequiredContentFields($ctype_name){

        $fields = $this->getContentFields($ctype_name);

        $req_fields = array();

        foreach($fields as $field){
            if ($field['options']['is_required']) {
                $req_fields[] = $field;
            }
        }

        return $req_fields;

    }

//============================================================================//
//============================================================================//

    public function getContentField($ctype_name, $id){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        return $this->getItemById($table_name, $id, function($item, $model){

            $item['options'] = cmsModel::yamlToArray($item['options']);

            if (!$item['is_system']){
                $item['options'] = array_merge($model->getDefaultContentFieldOptions(), $item['options']);
            }

            $item['groups_read'] = cmsModel::yamlToArray($item['groups_read']);
            $item['groups_edit'] = cmsModel::yamlToArray($item['groups_edit']);

            $fields_types = cmsForm::getAvailableFormFields(false);
            $field_class = "field" . string_to_camel('_', $item['type']);

            $item['parser_title'] = $fields_types[$item['type']];
            $item['parser'] = new $field_class($item['name'], $item);

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function reorderContentFields($ctype_name, $fields_ids_list){

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->reorderByList($table_name, $fields_ids_list);

        return true;

    }

//============================================================================//
//============================================================================//

    public function updateContentField($ctype_name, $id, $field){

        $content_table_name = $this->table_prefix . $ctype_name;
        $fields_table_name = $this->table_prefix . $ctype_name . '_fields';

        $field_old = $this->getContentField($ctype_name, $id);

        if (!$field_old['is_system']){
            if (($field_old['name'] != $field['name']) || ($field_old['type'] != $field['type'])){

                $field_class = "field" . string_to_camel('_', $field['type']);
                $field_handler = new $field_class(null, null);

                $sql = "ALTER TABLE  `{#}{$content_table_name}` CHANGE  `{$field_old['name']}` `{$field['name']}` {$field_handler->getSQL()}";
                $this->db->query($sql);

            }
        }

        return $this->update($fields_table_name, $id, $field);

    }

//============================================================================//
//============================================================================//

    public function deleteContentField($ctype_name_or_id, $id){

        if (is_numeric($ctype_name_or_id)){
            $ctype = $this->getContentType($ctype_name_or_id);
            $ctype_name = $ctype['name'];
        } else {
            $ctype_name = $ctype_name_or_id;
        }

        $field = $this->getContentField($ctype_name, $id);

        $content_table_name = $this->table_prefix . $ctype_name;
        $fields_table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->delete($fields_table_name, $id);
        $this->reorder($fields_table_name);

        $this->db->dropTableField($content_table_name, $field['name']);

        return true;

    }

//============================================================================//
//============================================================================//

    public function getContentFieldsets($ctype_id){

        if (is_numeric($ctype_id)){
            $ctype = $this->getContentType($ctype_id);
            $ctype_name = $ctype['name'];
        } else {
            $ctype_name = $ctype_id;
        }

        $table_name = $this->table_prefix . $ctype_name . '_fields';

        $this->groupBy('fieldset');
        $this->orderBy('fieldset');

        $fieldsets = $this->get($table_name, function($item, $model){
            $item = $item['fieldset'];
            return $item;
        }, false);

        if ($fieldsets[0] == '') { unset($fieldsets[0]); }

        return $fieldsets;

    }

//============================================================================//
//==============================   НАБОРЫ   ==================================//
//============================================================================//

    public function getContentDatasets($ctype_id=false, $only_visible=false){

        $table_name = 'content_datasets';

        if ($ctype_id) { $this->filterEqual('ctype_id', $ctype_id); }

        $this->orderBy('ordering');

        $this->useCache('content.datasets');

        $datasets = $this->get($table_name, function($item, $model){

            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            $item['filters'] = cmsModel::yamlToArray($item['filters']);
            $item['sorting'] = cmsModel::yamlToArray($item['sorting']);

            return $item;

        }, 'name');

        if ($only_visible && $datasets){
            $user = cmsUser::getInstance();
            foreach($datasets as $id=>$dataset){
                $is_user_view = $user->isInGroups($dataset['groups_view']);
                $is_user_hide = !empty($dataset['groups_hide']) && $user->isInGroups($dataset['groups_hide']) && !$user->is_admin;
                if (!$is_user_view || $is_user_hide) { unset($datasets[$id]); }
            }
        }

        return $datasets;

    }

    public function getContentDataset($id){

        $table_name = 'content_datasets';

        return $this->getItemById($table_name, $id, function($item, $model){

            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            $item['filters'] = cmsModel::yamlToArray($item['filters']);
            $item['sorting'] = cmsModel::yamlToArray($item['sorting']);

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function addContentDataset($dataset){

        $table_name = 'content_datasets';

        $dataset['ctype_id'] = (int)$dataset['ctype_id'];

        $this->filterEqual('ctype_id', $dataset['ctype_id']);

        $dataset['ordering'] = $this->getNextOrdering($table_name);

        $id = $this->insert($table_name, $dataset);

        cmsCache::getInstance()->clean('content.datasets');

        return $id;

    }

//============================================================================//
//============================================================================//

    public function updateContentDataset($id, $dataset){

        $table_name = 'content_datasets';

        $dataset['ctype_id'] = (int)$dataset['ctype_id'];

        $id = $this->update($table_name, $id, $dataset);

        cmsCache::getInstance()->clean('content.datasets');

        return $id;

    }

//============================================================================//
//============================================================================//

    public function reorderContentDatasets($fields_ids_list){

        $table_name = 'content_datasets';

        $this->reorderByList($table_name, $fields_ids_list);

        cmsCache::getInstance()->clean('content.datasets');

        return true;

    }

//============================================================================//
//============================================================================//

    public function deleteContentDataset($id){

        $this->delete('content_datasets', $id);

        cmsCache::getInstance()->clean('content.datasets');

        return true;

    }

//============================================================================//
//=============================   КОНТЕНТ   ==================================//
//============================================================================//

    public function resetFilters(){
        parent::resetFilters();
        $this->approved_filtered = false;
        return $this;
    }

    public function enableApprovedFilter(){
        $this->approved_filter_disabled = false;
        return $this;
    }

    public function disableApprovedFilter(){
        $this->approved_filter_disabled = true;
        return $this;
    }

    public function filterApprovedOnly(){

        if ($this->approved_filtered) { return $this; }

        // Этот фильтр может применяться при подсчете числа записей
        // и при выборке самих записей
        // используем флаг чтобы фильтр не применился дважды
        $this->approved_filtered = true;

        return $this->filterEqual('is_approved', 1);

    }

    public function filterByModeratorTask($moderator_id, $ctype_name){

        return $this->filter("(EXISTS (SELECT item_id FROM {#}moderators_tasks WHERE moderator_id='{$moderator_id}' AND ctype_name='{$ctype_name}' AND item_id=i.id))");

    }

//============================================================================//
//============================================================================//

    public function addContentItem($ctype, $item){

        $table_name = $this->table_prefix . $ctype['name'];

        $user = cmsUser::getInstance();
        $item['user_id'] = $user->id;

        $item['id'] = $this->insert($table_name, $item);

        if (!isset($item['slug'])){
            $item['slug'] = lang_slug( $item['id'].'-'.$item['title'] );
        }

        $this->update($table_name, $item['id'], array(
            'slug' => $item['slug']
        ));

        cmsCache::getInstance()->clean("content.list.{$ctype['name']}");

        return $item;

    }

//============================================================================//
//============================================================================//

    public function updateContentItem($ctype, $id, $item){

        $table_name = $this->table_prefix . $ctype['name'];

        if (!$ctype['is_fixed_url']){

            if ($ctype['is_auto_url']){ $item['slug'] = $id.'-'.$item['title']; }

            $item['slug'] = lang_slug( $item['slug'] );

            $this->update($table_name, $id, array( 'slug' => $item['slug'] ));

        }

        // удаляем поле SLUG из перечня полей для апдейта,
        // посколько оно могло быть изменено ранее
        $update_item = $item; unset($update_item['slug']);

        unset($update_item['user']);
        unset($update_item['user_nickname']);

        $this->update($table_name, $id, $update_item);

        cmsCache::getInstance()->clean("content.list.{$ctype['name']}");
        cmsCache::getInstance()->clean("content.item.{$ctype['name']}");

        return $item;

    }

    public function updateContentItemTags($ctype_name, $id, $tags){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array(
            'tags' => $tags
        ));
        
    }

//============================================================================//
//============================================================================//

    public function moveContentItemsToCategory($ctype_name, $category_id, $items_ids){

        $table_name = $this->table_prefix . $ctype_name;

        $this->filterIn('id', $items_ids)->updateFiltered($table_name, array(
            'category_id' => $category_id
        ));

        cmsCache::getInstance()->clean("content.list.{$ctype_name}");
        cmsCache::getInstance()->clean("content.item.{$ctype_name}");

        return true;

    }

//============================================================================//
//============================================================================//

    public function deleteContentItem($ctype_name, $id){

        $table_name = $this->table_prefix . $ctype_name;

        cmsCore::getController('activity')->deleteEntry('content', "add.{$ctype_name}", $id);

        cmsCore::getModel('comments')->deleteComments('content', $ctype_name, $id);
        cmsCore::getModel('rating')->deleteVotes('content', $ctype_name, $id);

        cmsCache::getInstance()->clean("content.list.{$ctype_name}");
        cmsCache::getInstance()->clean("content.item.{$ctype_name}");

        $this->closeModeratorTask($ctype_name, $id, false);

        return $this->delete($table_name, $id);

    }

    public function deleteUserContent($user_id){

        $ctypes = $this->getContentTypes();

        foreach($ctypes as $ctype){

            $items = $this->filterEqual('user_id', $user_id)->getContentItems($ctype['name']);

            foreach($items as $item){
                $this->deleteContentItem($ctype['name'], $item['id']);
            }

        }

    }

//============================================================================//
//============================================================================//

    public function getContentItemsCount($ctype_name){

        $table_name = $this->table_prefix . $ctype_name;

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }

        return $this->getCount($table_name);

    }

//============================================================================//
//============================================================================//

    public function getContentItems($ctype_name){

        $table_name = $this->table_prefix . $ctype_name;

        $this->select('u.nickname', 'user_nickname');
        $this->join('{users}', 'u', 'u.id = i.user_id');

        if (!$this->privacy_filter_disabled) { $this->filterPrivacy(); }
        if (!$this->approved_filter_disabled) { $this->filterApprovedOnly(); }

        if (!$this->order_by){ $this->orderBy('date_pub', 'desc'); }

        $this->useCache("content.list.{$ctype_name}");

        return $this->get($table_name, function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname']
            );

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function getContentItem($ctype_name, $id, $by_field='id'){

        $table_name = $this->table_prefix . $ctype_name;

        $this->select('u.nickname', 'user_nickname');

        $this->join('{users}', 'u', 'u.id = i.user_id');

        $this->useCache("content.item.{$ctype_name}");

        return $this->getItemByField($table_name, $by_field, $id, function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname']
            );

            return $item;

        }, $by_field);

    }

    public function getContentItemBySLUG($ctype_name, $slug){

        return $this->getContentItem($ctype_name, $slug, 'slug');

    }

//============================================================================//
//============================================================================//

    public function getUserContentItemsCount($ctype_name, $user_id){

        $this->filterEqual('user_id', $user_id);

        $count = $this->getContentItemsCount( $ctype_name );

        $this->resetFilters();

        return $count;

    }

    public function getUserContentCounts($user_id){

        $counts = array();

        $ctypes = $this->getContentTypes();

        $this->filterEqual('user_id', $user_id);

        foreach($ctypes as $ctype){

            $count = $this->getContentItemsCount( $ctype['name'] );

            if ($count) {

                $counts[ $ctype['name'] ] = array(
                    'count' => $count,
                    'is_in_list' => $ctype['options']['profile_on'],
                    'title' => $ctype['title']
                );

            }

        }

        $this->resetFilters();

        return $counts;

    }

//============================================================================//
//============================================================================//

    public function deleteCategory($ctype_name, $id){

        $this->filterEqual('category_id', $id);

        $items = $this->getContentItems($ctype_name);

        if ($items){
            foreach($items as $item){
                $this->deleteContentItem($ctype_name, $item['id']);
            }
        }

        parent::deleteCategory($ctype_name, $id);

    }

//============================================================================//
//============================================================================//

    public function getRatingTarget($ctype_name, $id){

        $table_name = $this->table_prefix . $ctype_name;

        $item = $this->getItemById($table_name, $id);

        return $item;

    }

    public function updateRating($ctype_name, $id, $rating){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array('rating' => $rating));

        cmsCache::getInstance()->clean("content.list.{$ctype_name}");
        cmsCache::getInstance()->clean("content.item.{$ctype_name}");

    }

//============================================================================//
//============================================================================//

    public function updateCommentsCount($ctype_name, $id, $comments_count){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array('comments' => $comments_count));

        cmsCache::getInstance()->clean("content.list.{$ctype_name}");
        cmsCache::getInstance()->clean("content.item.{$ctype_name}");

        return true;

    }

    public function getTargetItemInfo($ctype_name, $id){

        $item = $this->getContentItem($ctype_name, $id);

        if (!$item){ return false; }

        return array(
            'url' => href_to_rel($ctype_name, $item['slug'].'.html'),
            'title' => $item['title'],
            'is_private' => $item['is_private']
        );

    }

//============================================================================//
//============================================================================//

    public function toggleParentVisibility($parent_type, $parent_id, $is_hidden){

        $ctypes_names = $this->getContentTypesNames();

        $is_hidden = $is_hidden ? 1 : null;

        foreach($ctypes_names as $ctype_name){

            $table_name = $this->table_prefix . $ctype_name;

            $this->
                filterEqual('parent_type', $parent_type)->
                filterEqual('parent_id', $parent_id)->
                updateFiltered($table_name, array('is_parent_hidden' => $is_hidden));

        }

    }

//============================================================================//
//=========================    МОДЕРАТОРЫ   ==================================//
//============================================================================//

    public function getContentTypeModerators($ctype_name){

        $this->joinUser();

        $this->filterEqual('ctype_name', $ctype_name);

        $this->orderBy('id');

        return $this->get('moderators', false, 'user_id');

    }

    public function getContentTypeModerator($id){

        $this->joinUser();

        return $this->getItemById('moderators', $id);

    }

    public function userIsContentTypeModerator($ctype_name, $user_id){

        $this->filterEqual('ctype_name', $ctype_name);
        $this->filterEqual('user_id', $user_id);

        $is_moderator = (bool)$this->getCount('moderators');

        $this->resetFilters();

        return $is_moderator;

    }

    public function addContentTypeModerator($ctype_name, $user_id){

        $id = $this->insert('moderators', array(
            'ctype_name' => $ctype_name,
            'user_id' => $user_id,
            'date_assigned' => ''
        ));

        return $this->getContentTypeModerator($id);

    }

    public function deleteContentTypeModerator($ctype_name, $user_id){

        return $this->
                    filterEqual('ctype_name', $ctype_name)->
                    filterEqual('user_id', $user_id)->
                    deleteFiltered('moderators');

    }

    public function getNextModeratorId($ctype_name){

        return $this->
                    filterEqual('ctype_name', $ctype_name)->
                    orderBy('count_idle', 'asc')->
                    getFieldFiltered('moderators', 'user_id');

    }

    public function approveContentItem($ctype_name, $id, $moderator_user_id){

        $table_name = $this->table_prefix . $ctype_name;

        $this->update($table_name, $id, array(
            'is_approved' => 1,
            'approved_by' => $moderator_user_id,
            'date_approved' => ''
        ));

        return true;

    }

    public function getModeratorTask($ctype_name, $id){

        return $this->
                    filterEqual('ctype_name', $ctype_name)->
                    filterEqual('item_id', $id)->
                    getItem('moderators_tasks');

    }

    public function addModeratorTask($ctype_name, $user_id, $is_new_item, $item){

        $this->
            filterEqual('user_id', $user_id)->
            filterEqual('ctype_name', $ctype_name)->
            increment('moderators', 'count_idle');

        return $this->insert('moderators_tasks', array(
            'moderator_id' => $user_id,
            'author_id' => $item['user_id'],
            'item_id' => $item['id'],
            'ctype_name' => $ctype_name,
            'title' => $item['title'],
            'url' => href_to($ctype_name, $item['slug'].".html"),
            'date_pub' => '',
            'is_new_item' => $is_new_item
        ));

    }

    public function closeModeratorTask($ctype_name, $id, $is_approved){

        $user = cmsUser::getInstance();

        $counter_field = $is_approved ? 'count_approved' : 'count_deleted';

        $task = $this->getModeratorTask($ctype_name, $id);

        $this->
            filterEqual('user_id', $user->id)->
            filterEqual('ctype_name', $ctype_name)->
            increment('moderators', $counter_field);

        $this->
            filterEqual('user_id', $task['moderator_id'])->
            filterEqual('ctype_name', $ctype_name)->
            filterGt('count_idle', 0)->
            decrement('moderators', 'count_idle');

        return $this->
                filterEqual('ctype_name', $ctype_name)->
                filterEqual('item_id', $id)->
                deleteFiltered('moderators_tasks');

    }

//============================================================================//
//============================================================================//

}
