<?php
class cmsModel{

    public $name;

    public $db;

    //условия для выборок
    public $table      = '';
    public $select     = array('i.*');
    public $join       = '';
    public $where      = '';
    public $where_separator  = 'AND';
    public $group_by   = '';
    public $order_by   = '';
    public $limit      = 1000;
    public $perpage    = 50;

    public $keep_filters = false;
    public $filter_on  = false;

    protected $privacy_filter_disabled = false;
    protected $privacy_filtered = false;

    private $cache_key = false;

    public function __construct(){

        $core = cmsCore::getInstance();

        $this->name = str_replace('model_', '', get_called_class());

        $this->db = $core->db;

	}

//============================================================================//
//============================================================================//

    protected function useCache($key){
        $this->cache_key = $key;
    }

    protected function stopCache(){
        $this->cache_key = false;
    }

//============================================================================//
//============================================================================//

    public function getContentTableStruct(){

        return array(
            'id'            => array('type' => 'primary'),
            'title'         => array('type' => 'varchar', 'size' => 100),
            'content'       => array('type' => 'text'),
            'slug'          => array('type' => 'varchar', 'size' => 100),
            'seo_keys'      => array('type' => 'text'),
            'seo_desc'      => array('type' => 'text'),
            'tags'          => array('type' => 'varchar', 'size' => 1000),
            'date_pub'      => array('type' => 'timestamp', 'default_current' => true),
            'user_id'       => array('type' => 'int', 'index'=>true),
            'parent_id'     => array('type' => 'int', 'index'=>true),
            'parent_type'   => array('type' => 'varchar', 'size'=>32, 'index'=>true),
            'parent_title'  => array('type' => 'varchar', 'size' => 100),
            'parent_url'    => array('type' => 'varchar', 'size' => 255),
            'is_parent_hidden' => array('type' => 'bool', 'index'=>true),
            'category_id'   => array('type' => 'int', 'index'=>true, 'default' => 1),
            'is_comments_on'=> array('type' => 'bool'),
            'comments'      => array('type' => 'int', 'index'=>true, 'default' => 0),
            'rating'        => array('type' => 'int', 'index'=>true, 'default' => 0),
            'is_approved'   => array('type' => 'tinyint', 'index'=>true, 'default' => 1),
            'approved_by'    => array('type' => 'int', 'index'=>true),
            'date_approved' => array('type' => 'timestamp'),
            'is_private'    => array('type' => 'bool', 'index'=>true, 'default' => 0),
        );

    }

    public function getFieldsTableStruct(){

        return array(
            'id'            => array('type' => 'primary'),
            'ctype_id'      => array('type' => 'int'),
            'name'          => array('type' => 'varchar', 'size' => 20),
            'title'         => array('type' => 'varchar', 'size' => 100),
            'hint'          => array('type' => 'varchar', 'size' => 200),
            'ordering'      => array('type' => 'int', 'index'=>true),
            'fieldset'      => array('type' => 'varchar', 'size' => 32),
            'type'          => array('type' => 'varchar', 'size' => 16),
            'is_in_list'    => array('type' => 'bool'),
            'is_in_item'    => array('type' => 'bool'),
            'is_in_filter'  => array('type' => 'bool'),
            'is_private'    => array('type' => 'bool'),
            'is_fixed'      => array('type' => 'bool'),
            'is_fixed_type' => array('type' => 'bool'),
            'is_system'     => array('type' => 'bool'),
            'values'        => array('type' => 'text'),
            'options'       => array('type' => 'text'),
            'groups_read'   => array('type' => 'text'),
            'groups_edit'   => array('type' => 'text'),
        );

    }

//============================================================================//
//============================================================================//

    public function getRootCategory($ctype_name){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        return $this->db->getFields($table_name, 'parent_id=0');

    }

    public function getCategory($ctype_name, $id, $by_field='id'){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->useCache("content.categories");

        $category = $this->getItemByField($table_name, $by_field, $id);

        if (!$category) { return false; }

        $category['path'] = $this->getCategoryPath($ctype_name, $category);

        return $category;

    }

    public function getCategoryBySLUG($ctype_name, $slug){

        return $this->getCategory($ctype_name, $slug, 'slug');

    }

    public function getCategorySLUG($category){

        $slug = '';

        foreach($category['path'] as $c){
            if ($c['id'] == 1) { continue; }
            if ($slug) { $slug .= '/'; }
            $slug .= lang_slug( $c['title'] );
        }

        return $slug;

    }

//============================================================================//
//============================================================================//

    public function getCategoriesTree($ctype_name, $is_show_root=true) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        if (!$is_show_root){
            $this->filterGt('parent_id', 0);
        }

        $this->orderBy('ns_left');

        $this->useCache("content.categories");

        return $this->get($table_name, function($node, $model){
            if ($node['ns_level']==0) { $node['title'] = LANG_ROOT_CATEGORY; }
            return $node;
        });

    }

//============================================================================//
//============================================================================//

    public function getSubCategories($ctype_name, $parent_id) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->filterEqual('parent_id', $parent_id);
        $this->orderBy('title');

        return $this->get($table_name);

    }

    public function getSubCategoriesTree($ctype_name, $parent_id=1, $level=1) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $parent = $this->getCategory($ctype_name, $parent_id);

        $this->
            filterGt('ns_left', $parent['ns_left'])->
            filterLt('ns_right', $parent['ns_right']);

        if ($level){
            $this->filterLtEqual('ns_level', $parent['ns_level'] + $level);
        }

        $this->orderBy('ns_left');

        $this->useCache("content.categories");

        return $this->get($table_name);

    }

//============================================================================//
//============================================================================//

    public function getCategoryPath($ctype_name, $category) {

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        if (!isset($category['ns_left'])){
            $category = $this->getCategory($ctype_name, $category['id']);
        }

        $this->
            filterLtEqual('ns_left', $category['ns_left'])->
            filterGtEqual('ns_right', $category['ns_right'])->
            filterLtEqual('ns_level', $category['ns_level'])->
            filterGt('ns_level', 0)->
            orderBy('ns_left');

        $this->useCache("content.categories");

        return $this->get($table_name);

    }

//============================================================================//
//============================================================================//

    public function addCategory($ctype_name, $category){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->db->nestedSets->setTable($table_name);

        $category['id'] = $this->db->nestedSets->addNode($category['parent_id']);

        if (!$category['id']){ return false; }

        $category['title'] = $this->db->escape($category['title']);

        $this->update($table_name, $category['id'], array(
            'title' => $category['title']
        ));

        $category['path'] = $this->getCategoryPath($ctype_name, $category);

        $category['slug'] = $this->getCategorySLUG($category);

        $this->update($table_name, $category['id'], array(
            'slug' => $category['slug']
        ));

        cmsCache::getInstance()->clean("content.categories");

        return $category;

    }

//============================================================================//
//============================================================================//

    public function updateCategory($ctype_name, $id, $category){

        cmsCache::getInstance()->clean("content.categories");

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $category_old = $this->getCategory($ctype_name, $id);

        $this->update($table_name, $category['id'], array(
            'title' => $category['title']
        ));

        if ($category_old['parent_id'] != $category['parent_id']){
            $this->db->nestedSets->setTable($table_name);
            $this->db->nestedSets->moveNode($id, $category['parent_id']);
        }

        $category['path'] = $this->getCategoryPath($ctype_name, array('id' => $id));
        $category['slug'] = $this->getCategorySLUG($category);

        $this->update($table_name, $category['id'], array(
            'slug' => $category['slug']
        ));

        $subcats = $this->getSubCategories($ctype_name, $id, false);

        if ($subcats){
            foreach($subcats as $subcat){
                $subcat['path'] = $this->getCategoryPath($ctype_name, array('id' => $subcat['id']));
                $subcat['slug'] = $this->getCategorySLUG($subcat);
                $this->update($table_name, $subcat['id'], array('slug' => $subcat['slug']));
            }
        }

        return $category;

    }

//============================================================================//
//============================================================================//

    public function updateCategoryTree($ctype_name, $tree, $categories_count){

        cmsCache::getInstance()->clean("content.categories");

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->updateCategoryTreeNode($ctype_name, $tree);

        $root_keys = array(
            'ns_left' => 1,
            'ns_right' => 1 + ($categories_count*2) + 1
        );

        $this->update($table_name, 1, $root_keys);

        return true;

    }

    public function updateCategoryTreeNode($ctype_name, $tree){

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        foreach($tree as $node){

            $this->update($table_name, $node['key'], array(
                'parent_id' => $node['parent_key'],
                'ns_left' => $node['left'],
                'ns_right' => $node['right'],
                'ns_level' => $node['level'],
            ));

            $path = $this->getCategoryPath($ctype_name, array(
                'id' => $node['key'],
                'parent_id' => $node['parent_key'],
                'ns_left' => $node['left'],
                'ns_right' => $node['right'],
                'ns_level' => $node['level']
            ));

            $slug = $this->getCategorySLUG(array(
                'path' => $path,
                'title' => $node['title']
            ));

            $this->update($table_name, $node['key'], array(
                'slug' => $slug
            ));

            if (!empty($node['children'])){
                $this->updateCategoryTreeNode($ctype_name, $node['children']);
            }

        }

        return true;

    }

//============================================================================//
//============================================================================//

    public function deleteCategory($ctype_name, $id){

        //
        // Эта функция должна быть переопределена и вызываться
        // из дочернего класса чтобы после нее удалять все записи
        // из категории
        //

        $table_name = $this->table_prefix . $ctype_name . '_cats';

        $this->db->nestedSets->setTable($table_name);
        $this->db->nestedSets->deleteNode($id);

        cmsCache::getInstance()->clean("content.categories");

        return true;

    }

//============================================================================//
//============================================================================//

    public function delete($table_name, $id, $by_field='id'){
        $this->filterEqual($by_field, $id);
        return $this->deleteFiltered($table_name);
    }

    public function deleteFiltered($table_name){
        $where = $this->where;
        $this->resetFilters();
        return $this->db->delete($table_name, $where);
    }

//============================================================================//
//============================================================================//

    public function update($table_name, $id, $data){
        $this->filterEqual('id', $id);
        return $this->updateFiltered($table_name, $data);
    }

    public function updateFiltered($table_name, $data){
        $where = $this->where;
        $this->resetFilters();
        return $this->db->update($table_name, $where, $data);
    }

//============================================================================//
//============================================================================//

    public function insert($table_name, $data){
        return $this->db->insert($table_name, $data);
    }

    public function insertOrUpdate($table_name, $insert_data, $update_data = false){
        return $this->db->insertOrUpdate($table_name, $insert_data, $update_data);
    }

//============================================================================//
//============================================================================//

    public function lockFilters(){
        $this->keep_filters = true;
        return $this;
    }

    public function unlockFilters(){
        $this->keep_filters = false;
        return $this;
    }

    public function resetFilters(){

        if ($this->keep_filters) { return; }

        $this->select       = array('i.*');
        $this->where        = '';
        $this->group_by     = '';
        $this->order_by     = '';
        $this->limit        = '';
        $this->join         = '';
        $this->filter_on    = false;

        $this->privacy_filtered = false;

        return $this;

    }

    public function filter($condition){
        if ($this->filter_on){
            $this->where .= " {$this->where_separator} ({$condition})";
        } else {
            $this->where .= "({$condition})";
            $this->filter_on = true;
        }
        return $this;
    }

    public function filterStart(){
        $this->where .= ' ( ';
        $this->filter_on = false;
        return $this;
    }

    public function filterEnd(){
        $this->where .= ' ) ';
        $this->filter_on = false;
        return $this;
    }

    public function filterAnd(){
        $this->where .= ' AND ';
        return $this;
    }

    public function filterOr(){
        $this->where .= ' OR ';
        return $this;
    }

    public function filterNotNull($field){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $this->filter("$field IS NOT NULL");
        return $this;
    }

    public function filterIsNull($field){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $this->filter("$field IS NULL");
        return $this;
    }

    public function filterEqual($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        if (is_null($value)){
            $this->filter("$field IS NULL");
        } else {
            $value = $this->db->escape($value);
            $this->filter("$field = '$value'");
        }
        return $this;
    }

    public function filterFunc($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $this->filter("$field = $value");
        return $this;
    }

    public function filterNotEqual($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        if (is_null($value)){
            $this->filter("$field NOT IS NULL");
        } else {
            $value = $this->db->escape($value);
            $this->filter("$field <> '$value'");
        }
        return $this;
    }

    public function filterGt($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field > '$value'");
        return $this;
    }

    public function filterLt($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field < '$value'");
        return $this;
    }

    public function filterGtEqual($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field >= '$value'");
        return $this;
    }

    public function filterLtEqual($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field <= '$value'");
        return $this;
    }

    public function filterLike($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $this->filter("$field LIKE '$value'");
        return $this;
    }

    public function filterBetween($field, $start, $end){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $start = $this->db->escape($start);
        $end = $this->db->escape($end);
        $this->filter("$field BETWEEN '$start' AND '$end'");
        return $this;
    }

    public function filterDateYounger($field, $value, $interval='DAY'){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $interval = $this->db->escape($interval);
        $this->filter("$field >= DATE_SUB(NOW(), INTERVAL {$value} {$interval})");
        return $this;
    }

    public function filterDateOlder($field, $value, $interval='DAY'){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $value = $this->db->escape($value);
        $interval = $this->db->escape($interval);
        $this->filter("$field < DATE_SUB(NOW(), INTERVAL {$value} {$interval})");
        return $this;
    }

    public function filterTimestampGt($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $field = "UNIX_TIMESTAMP({$field})";
        $value = $this->db->escape($value);
        $this->filter("{$field} > '{$value}'");
        return $this;
    }

    public function filterTimestampLt($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $field = "UNIX_TIMESTAMP({$field})";
        $value = $this->db->escape($value);
        $this->filter("{$field} < '{$value}'");
        return $this;
    }

    public function filterIn($field, $value){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        if (is_array($value)){
            foreach($value as $k=>$v){
                $v = $this->db->escape($v);
                $value[$k] = "'{$v}'";
            }
            $value = implode(',', $value);
        } else {
            $value = $this->db->escape($value);
            $value = "'{$value}'";
        }
        $this->filter("{$field} IN ({$value})");
        return $this;
    }

    public function filterCategory($ctype_name, $category, $is_recursive=false){

        if (!$is_recursive){

            $this->filterEqual('category_id', $category['id']);

        } else {

            $table_name = $this->table_prefix . $ctype_name . '_cats';

            $this->join($table_name, 'c', 'c.id = i.category_id');

            $this->filterGtEqual('c.ns_left', $category['ns_left']);
            $this->filterLtEqual('c.ns_right', $category['ns_right']);

        }

        return $this;

    }

    public function disablePrivacyFilter(){
        $this->privacy_filter_disabled = true;
        return $this;
    }

    public function enablePrivacyFilter(){
        $this->privacy_filter_disabled = false;
        return $this;
    }

    public function filterPrivacy(){

        $user = cmsUser::getInstance();

        if ($this->privacy_filtered) { return $this; }

        // Этот фильтр может применяться при подсчете числа записей
        // и при выборке самих записей
        // используем флаг чтобы фильтр не применился дважды
        $this->privacy_filtered = true;

        return $this->filter("(i.is_private = 0 OR i.user_id = {$user->id} OR (i.is_private = 1 AND EXISTS (SELECT id FROM {users}_friends WHERE user_id={$user->id} AND friend_id=i.user_id AND is_mutual=1)))");

    }

    public function filterHiddenParents(){
        return $this->filterIsNull('is_parent_hidden');
    }

    public function filterFriends($user_id){

        $user_id = intval($user_id);

        $this->joinInner('{users}_friends', 'f', "friend_id = i.user_id AND f.is_mutual = 1 AND f.user_id = '{$user_id}'");

        return $this;

    }

    public function applyDatasetFilters($dataset, $ignore_sorting=false){

        if (!empty ($dataset['filters'])){

            foreach($dataset['filters'] as $filter){

                if (($filter['value'] === '') && !in_array($filter['condition'], array('nn', 'ni'))) { continue; }
                if (empty($filter['condition'])) { continue; }

                if ($filter['value'] !== '') { $filter['value'] = string_replace_user_properties($filter['value']); }

                switch($filter['condition']){

                    // общие условия
                    case 'eq': $this->filterEqual($filter['field'], $filter['value']); break;
                    case 'gt': $this->filterGt($filter['field'], $filter['value']); break;
                    case 'lt': $this->filterLt($filter['field'], $filter['value']); break;
                    case 'ge': $this->filterGtEqual($filter['field'], $filter['value']); break;
                    case 'le': $this->filterLtEqual($filter['field'], $filter['value']); break;
                    case 'nn': $this->filterNotNull($filter['field']); break;
                    case 'ni': $this->filterIsNull($filter['field']); break;

                    // строки
                    case 'lk': $this->filterLike($filter['field'], '%'.$filter['value'].'%'); break;
                    case 'lb': $this->filterLike($filter['field'], $filter['value'] . '%'); break;
                    case 'lf': $this->filterLike($filter['field'], '%' . $filter['value']); break;

                    // даты
                    case 'dy': $this->filterDateYounger($filter['field'], $filter['value']); break;
                    case 'do': $this->filterDateOlder($filter['field'], $filter['value']); break;

                }

            }

        }

        if (!empty ($dataset['sorting']) && !$ignore_sorting){
            $this->orderBy($dataset['sorting']['by'], $dataset['sorting']['to']);
        }

        return true;

    }

    public function select($field, $as=false){
        $this->select[] = $as ? "{$field} as {$as}" : $field;
        return $this;
    }

    public function join($table_name, $as, $on){
        $this->join .= "JOIN {#}{$table_name} as {$as} ON {$on}\n";
        return $this;
    }

    public function joinLeft($table_name, $as, $on){
        $this->join .= "LEFT JOIN {#}{$table_name} as {$as} ON {$on}\n";
        return $this;
    }

    public function joinRight($table_name, $as, $on){
        $this->join .= "RIGHT JOIN {#}{$table_name} as {$as} ON {$on}\n";
        return $this;
    }

    public function joinInner($table_name, $as, $on){
        $this->join .= "INNER JOIN {#}{$table_name} as {$as} ON {$on}\n";
        return $this;
    }

    public function joinOuter($table_name, $as, $on){
        $this->join .= "OUTER JOIN {#}{$table_name} as {$as} ON {$on}\n";
        return $this;
    }

    public function joinUser($on_field='user_id', $user_fields=array()){

        if (!$user_fields){
            $user_fields = array(
                'u.nickname' => 'user_nickname',
                'u.avatar' => 'user_avatar',
            );
        }

        foreach($user_fields as $field => $alias){
            $this->select($field, $alias);
        }

        $this->join('{users}', 'u', "u.id = i.{$on_field}");

        return $this;

    }

    public function groupBy($field){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $this->group_by = $field;
        return $this;
    }

    public function orderBy($field, $direction=''){
        if (!strstr($field, '.')){ $field = 'i.' . $field; }
        $this->order_by = "{$field} {$direction}";
        return $this;
    }

    public function limit($from, $howmany='') {
        $this->limit = (int)$from;
        if ($this->limit < 0) { $this->limit = 0; }
        if ($howmany){
            if ((int)$howmany <= 0){ $howmany = 15; }
            $this->limit .= ', '. (int)$howmany;
        }
        return $this;
    }

    public function limitPage($page, $perpage=false) {
        if (!$perpage) { $perpage = $this->perpage; }
        $this->limit(($page-1)*$perpage, $perpage);
        return $this;
    }

    public function setPerPage($perpage){
        $this->perpage = $perpage;
        return $this;
    }

//============================================================================//
//============================================================================//

    public function getField($table_name, $row_id, $field_name){

        $this->filterEqual('id', $row_id);
        return $this->fieldFiltered($table_name, $field_name);

    }

    public function getFieldFiltered($table_name, $field_name){

        $this->select = array("i.{$field_name} as {$field_name}");

        $this->table = $table_name;

        $this->limit(1);

        $sql = $this->getSQL();

        $this->resetFilters();

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){ return false; }

        $item = $this->db->fetchAssoc($result);

        $this->db->freeResult($result);

        return $item[ $field_name ];

    }

//============================================================================//
//============================================================================//

    public function getItem($table_name, $item_callback=false){

        $select = implode(', ', $this->select);

        $sql = "SELECT {$select}
                FROM {#}{$table_name} i
                ";

        if ($this->join){ $sql .= $this->join; }

        $sql .= "WHERE {$this->where}
                 LIMIT 1";

        $this->resetFilters();

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key){

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache = cmsCache::getInstance();

            if (false !== ($item = $cache->get($cache_key))){
                $this->stopCache();
                return $item;
            }

        }

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){ return false; }

        $item = $this->db->fetchAssoc($result);

        if(is_callable($item_callback)){
            $item = $item_callback( $item, $this );
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key){
            $cache->set($cache_key, $item);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        return $item;

    }

    public function getItemById($table_name, $id, $item_callback=false){
        $this->filterEqual('id', $id);
        return $this->getItem($table_name, $item_callback);
    }

    public function getItemByField($table_name, $field_name, $field_value, $item_callback=false){
        $this->filterEqual($field_name, $field_value);
        return $this->getItem($table_name, $item_callback);
    }

//============================================================================//
//============================================================================//

    public function getCount($table_name, $by_field='id'){

        $sql = "SELECT COUNT(i.{$by_field}) as count
                FROM {#}{$table_name} i
                ";

        if ($this->join){ $sql .= $this->join; }

        if ($this->where){ $sql .= "WHERE {$this->where}\n"; }

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key){

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache = cmsCache::getInstance();

            if (false !== ($result = $cache->get($cache_key))){
                $this->stopCache();
                return $result;
            }

        }

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){
            $count = 0;
        } else {
            $item = $this->db->fetchAssoc($result);
            $count = (int)$item['count'];
        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key){
            $cache->set($cache_key, $count);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        return $count;

    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает записи из базы, применяя все наложенные ранее фильтры
     * @return array
     */
    public function get($table_name, $item_callback=false, $key_field='id'){

        $this->table = $table_name;

        $sql = $this->getSQL();

        // сбрасываем фильтры
        $this->resetFilters();

        // если указан ключ кеша для этого запроса
        // то пробуем получить результаты из кеша
        if ($this->cache_key){

            $cache_key = $this->cache_key . '.' . md5($sql);
            $cache = cmsCache::getInstance();

            if (false !== ($items = $cache->get($cache_key))){
                $this->stopCache();
                return $items;
            }

        }

        $result = $this->db->query($sql);

        // если запрос ничего не вернул, возвращаем ложь
        if (!$this->db->numRows($result)){ return false; }

        $items = array();

        // перебираем все вернувшиеся строки
        while($item = $this->db->fetchAssoc($result)){

            $key = $key_field ? $item[$key_field] : false;

            // если задан коллбек для обработки строк,
            // то пропускаем строку через него
            if (is_callable($item_callback)){
                $item = $item_callback( $item, $this );
                if ($item===false){ continue; }
            }

            // добавляем обработанную строку в результирующий массив
            if ($key){
                $items[$key] = $item;
            } else {
                $items[] = $item;
            }

        }

        // если указан ключ кеша для этого запроса
        // то сохраняем результаты в кеше
        if ($this->cache_key){
            $cache->set($cache_key, $items);
            $this->stopCache();
        }

        $this->db->freeResult($result);

        // возвращаем строки
        return $items;

    }

//============================================================================//
//============================================================================//

    public function getSQL(){

        $select = implode(', ', $this->select);

        $sql = "SELECT {$select}
                FROM {#}{$this->table} i
                ";

        if ($this->join){ $sql .= $this->join; }

        if ($this->where){ $sql .= "WHERE {$this->where}\n"; }

        if ($this->group_by){ $sql .= "GROUP BY {$this->group_by}\n"; }

        if ($this->order_by){ $sql .= "ORDER BY {$this->order_by}\n"; }

        if ($this->limit){ $sql .= "LIMIT {$this->limit}\n"; }

        return $sql;

    }

//============================================================================//
//============================================================================//

    public function getMax($table, $field, $default=0){

        $sql = "SELECT {$field}
                FROM {#}{$table} i
                ";

        if ($this->where) { $sql .= "WHERE {$this->where}\n"; }

        $sql .= "ORDER BY {$field} DESC
                 LIMIT 1";

        $result = $this->db->query($sql);

        $this->resetFilters();

        if (!$this->db->numRows($result)){ return $default; }

        $max = $this->db->fetchAssoc($result);

        $this->db->freeResult($result);

        return $max[$field];

    }

    /**
     * Возвращает максимальный порядковый номер в таблице
     * @param string $table
     * @param string $where
     * @return int
     */
    public function getMaxOrdering($table){

        return $this->getMax($table, 'ordering');

    }

    /**
     * Возращает следующий порядковый номер в таблице для новых записей
     * @param string $table
     * @param string $where
     * @return int
     */
    public function getNextOrdering($table){

        return $this->getMaxOrdering($table) + 1;

    }

    /**
     * Пересчитывает порядковые номера в таблице
     * @param string $table_name
     * @param string $where
     * @return bool
     */
    public function reorder($table_name){

        $sql = "SELECT id, ordering
                FROM {#}{$table_name} i
                ";

        if ($this->where) { $sql .= "WHERE {$where}\n"; }

        $sql .= "ORDER BY ordering";

        $result = $this->db->query($sql);

        if (!$this->db->numRows($result)){ return false; }

        $ordering = 0;

        while($item = $this->db->fetchAssoc($result)){

            $ordering += 1;
            $this->db->query("UPDATE {#}{$table_name} SET ordering = {$ordering} WHERE id = {$item['id']}");

        }

        $this->resetFilters();

        $this->db->freeResult($result);

        return true;

    }

    /**
     * Расставляет порядковые номера для списка из ID записей
     * @param string $table_name
     * @param string $list
     * @param array $additional_fields Список дополнительных полей и их значений, которые нужно обновлять вместе с ordering
     * @return bool
     */
    public function reorderByList($table_name, $list, $additional_fields=false){

        $ordering = 0;

        $additional_set = array();

        if (is_array($additional_fields)){
            foreach($additional_fields as $field=>$value){
                $value = $this->db->escape($value);
                $additional_set[] = "{$field} = '{$value}'";
            }
        }

        if ($additional_set){
            $additional_set = ', ' . implode(', ', $additional_set);
        } else {
            $additional_set = '';
        }

        foreach($list as $id){

            $ordering += 1;

            $id = $this->db->escape($id);

            $query = "UPDATE {#}{$table_name}
                      SET ordering = '{$ordering}' {$additional_set}
                      WHERE id = '{$id}'";

            $this->db->query($query);

        }

        return true;

    }

//============================================================================//
//============================================================================//

    /**
     * Применяет к модели фильтры, переданные из просмотра
     * таблицы со списком записей
     * @param array $grid
     * @param array $filter
     * @return bool
     */
    public function applyGridFilter($grid, $filter){

        // применяем сортировку
        if (!empty($filter['order_by'])) {
            if (!empty($grid['columns'][$filter['order_by']]['order_by'])){
                $filter['order_by'] = $grid['columns'][$filter['order_by']]['order_by'];
            }
            $this->orderBy($filter['order_by'], $filter['order_to']);
        }

        // устанавливаем страницу
        if (!empty($filter['page'])){
            $perpage = !empty($filter['perpage']) ? intval($filter['perpage']) : $this->perpage;
            $this->limitPage(intval($filter['page']), $perpage);
        }

        //
        // проходим по каждой колонке таблицы
        // и проверяем не передан ли фильтр для нее
        //
        foreach($grid['columns'] as $field => $column){
            if (isset($column['filter']) && $column['filter'] != 'none' && $column['filter'] != false){

                if (!empty($filter[$field])){

                    if (!empty($column['filter_by'])){
                        $filter_field = $column['filter_by'];
                    } else {
                        $filter_field = $field;
                    }

                    switch ($column['filter']){
                        case 'exact': $this->filterEqual($filter_field, $filter[$field]); break;
                        case 'like': $this->filterLike($filter_field, "%{$filter[$field]}%"); break;
                    }

                }

            }
        }

        return;

    }

//============================================================================//
//============================================================================//

    /**
     * Сортирует элементы массива $items в виде плоского дерева
     * на основании связей через parent_id
     * @param array $items
     * @param array $result_tree
     * @param int $parent_id
     * @param int $level
     */
    public function buildTreeRecursive($items, &$result_tree, $parent_id=0, $level=1){
        $level++;

        foreach($items as $num=>$item){
            if ($item['parent_id']==$parent_id){
                $item['level'] = $level-1;
                if (!isset($result_tree[$item['id']])){
                    $result_tree[$item['id']] = $item;
                }
                $this->buildTreeRecursive($items, $result_tree, $item['id'], $level);
            }
        }
    }

//============================================================================//
//============================================================================//

    public function increment($table, $field, $step=1){

        $sql = "UPDATE {#}{$table} i
                SET i.{$field} = i.{$field} + {$step}
                ";

        if ($this->where) { $sql .= "WHERE {$this->where}"; }

        $this->resetFilters();

        return $this->db->query($sql);

    }

    public function decrement($table, $field, $step=1){
        return $this->increment($table, $field, $step * -1);
    }

//============================================================================//
//============================================================================//

    /**
     * Преобразует массив в YAML
     * @param array $array
     * @return string
     */
    static public function arrayToYaml($array) {

        cmsCore::loadLib('spyc.class');

        $yaml = Spyc::YAMLDump($array,2,40);

        return $yaml;

    }

    /**
     * Преобразует YAML в массив
     * @param string $yaml
     * @return array
     */
    static public function yamlToArray($yaml) {

        cmsCore::loadLib('spyc.class');

        $array = Spyc::YAMLLoad($yaml);

        return $array;

    }

}

