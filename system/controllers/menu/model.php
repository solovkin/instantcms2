<?php

class modelMenu extends cmsModel{

//============================================================================//
//============================================================================//

    public function addMenu($item){

        $id = $this->insert('menu', $item);

        cmsCache::getInstance()->clean("menu.items");

        return $id;

    }

//============================================================================//
//============================================================================//

    public function updateMenu($id, $item){

        cmsCache::getInstance()->clean("menu.items");

        return $this->update('menu', $id, $item);

    }

//============================================================================//
//============================================================================//

    public function deleteMenu($id){

        $this->delete('menu', $id);

        $this->filterEqual('menu_id', $id)->deleteFiltered('menu_items');

        cmsCache::getInstance()->clean("menu.items");

    }

//============================================================================//
//============================================================================//

    public function getMenus(){

        $items = array();

        $sql = "SELECT *
                FROM {#}menu
                ORDER BY id ASC";

        $result = $this->db->query($sql);

        // если запрос ничего не вернул, возвращаем ложь
        if (!$this->db->numRows($result)){ return false; }

        // перебираем все вернувшиеся строки
        while($item = $this->db->fetchAssoc($result)){

            // добавляем обработанную строку в результирующий массив
            $items[$item['id']] = $item;

        }

        // возвращаем строки
        return $items;

    }

//============================================================================//
//============================================================================//

    public function getMenu($id, $by_field='id'){

        $this->useCache("menu.menus");

        return $this->getItemByField('menu', $by_field, $id);

    }

//============================================================================//
//============================================================================//

    public function getMenuItems($menu_id, $parent_id=false){

        $this->select('COUNT(childs.id)', 'childs_count');

        $this->joinLeft('menu_items', 'childs', 'childs.parent_id = i.id');

        $this->filterEqual('menu_id', $menu_id);

        if ($parent_id !== false){
            $this->filterEqual('parent_id', $parent_id);
        }

        $this->groupBy('id');

        $this->orderBy('ordering', 'asc');

        $this->useCache("menu.items");

        return $this->get('menu_items', function($item, $model){
            if ($item['options']){
                $item['options'] = cmsModel::yamlToArray($item['options']);
            }
            $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
            $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            return $item;
        });

    }

//============================================================================//
//============================================================================//

    public function getMenuItemsTree($menu_id, $parse_hooks=true){

        $result = $this->getMenuItems($menu_id);

        if (!$result){ return false; }

        $items = array();

        $user = cmsUser::getInstance();

        // перебираем все вернувшиеся пункты меню
        foreach($result as $item){

            $is_root_added = false;

            if ($item['groups_view'] && !$user->isInGroups($item['groups_view'])) { continue; }
            if ($item['groups_hide'] && $user->isInGroups($item['groups_hide'])) { continue; }

            $hook_result = array('items' => false);

            if ($parse_hooks){

                // если URL пункта меню содержит шаблон {controller:action}
                if (preg_match('/^{([a-z0-9]+):*([a-z0-9]*)}$/i', $item['url'], $matches)){

                    // то вызываем хук menu указанного контроллера
                    $controller = $matches[1];
                    $action = $matches[2];

                    $hook_result = cmsEventsManager::hook("menu_{$controller}", array(
                        'action' => $action,
                        'menu_item_id'  => $item['id'],
                        'menu_item_url' => $item['url']
                    ));

                    // если хук вернул результат
                    if ($hook_result){

                        // получаем новый URL пункта меню
                        $item['url'] = isset($hook_result['url']) ? $hook_result['url'] : '';

                        if (isset($hook_result['counter'])) {
                            $item['counter'] = $hook_result['counter'];
                        }

                        if (isset($hook_result['items']) && is_array($hook_result['items'])) {
                            $item['childs_count'] = sizeof($hook_result['items']);
                        }

                        $is_root_added = true;

                    } else {
                        continue;
                    }

                }

                if (!$is_root_added && mb_strpos($item['url'], 'http://')===false) { $item['url'] = href_to($item['url']); }

                // если URL пункта меню содержит свойство пользователя
                $item['url'] = string_replace_user_properties($item['url']);

            }

            // добавляем обработанную строку в результирующий массив
            $items[$item['id']] = $item;

            // получаем дополнительные пункты меню
            if (isset($hook_result['items']) && is_array($hook_result['items'])) {
                foreach($hook_result['items'] as $i) {
                    $i['menu_id'] = $item['menu_id'];
                    $items[$i['id']] = $i;
                }
            }

        }

        $tree = array();

        $this->buildTreeRecursive($items, $tree);

        // возвращаем дерево
        return $tree;

    }

//============================================================================//
//============================================================================//

    public function getMenuItem($id){

        return $this->getItemById('menu_items', $id, function($item, $model){
            if ($item['options']){
                $item['options'] = cmsModel::yamlToArray($item['options']);
                $item['groups_view'] = cmsModel::yamlToArray($item['groups_view']);
                $item['groups_hide'] = cmsModel::yamlToArray($item['groups_hide']);
            }
            return $item;
        });

    }

//============================================================================//
//============================================================================//

    public function reorderMenuItems($items_ids_list){

        $this->reorderByList('menu_items', $items_ids_list);

        cmsCache::getInstance()->clean("menu.items");

        return true;

    }

//============================================================================//
//============================================================================//

    public function addMenuItem($item){

        $this->filterEqual('parent_id', $item['parent_id']);

        $item['ordering'] = $this->getNextOrdering('menu_items');

        if (is_array($item['options'])){
            $item['options'] = cmsModel::arrayToYaml($item['options']);
        }

        $id = $this->insert('menu_items', $item);

        cmsCache::getInstance()->clean("menu.items");

        return $id;

    }

//============================================================================//
//============================================================================//

    public function updateMenuItem($id, $item){

        if (is_array($item['options'])){
            $item['options'] = cmsModel::arrayToYaml($item['options']);
        }

        cmsCache::getInstance()->clean("menu.items");

        return $this->update('menu_items', $id, $item);

    }

//============================================================================//
//============================================================================//

    public function deleteMenuItem($id){

        $item = $this->getMenuItem($id);

        $tree = $this->getMenuItemsTree($item['menu_id'], false);

        $level = false;
        $node_start = false;
        $to_delete = array($id);
        $to_reorder = array();

        foreach($tree as $item){

            if ($item['id']==$id){
                $node_start = true;
                $level = $item['level'];
                continue;
            }

            if ($node_start){
                if ($item['level'] > $level) {
                    $to_delete[] = $item['id'];
                    continue;
                } else {
                    $node_start = false;
                }
            }

            $to_reorder[] = $item['id'];

        }

        foreach($to_delete as $item_id){
            $this->delete('menu_items', $item_id);
        }

        $this->reorderByList('menu_items', $to_reorder);

        cmsCache::getInstance()->clean("menu.items");

        return true;

    }


//============================================================================//
//============================================================================//



}
