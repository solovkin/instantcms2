<?php

class modelUsers extends cmsModel{

//============================================================================//
//========================    ПОЛЬЗОВАТЕЛИ   =================================//
//============================================================================//

    public function getUsersCount(){

        return $this->getCount('{users}');

    }

//============================================================================//
//============================================================================//

    public function filterGroup($group_id){
        return $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$group_id}'");
    }

    public function filterGroups($groups_list){
        $groups_list = implode(',', $groups_list);
        return $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id IN ({$groups_list})");
    }

    public function filterGroupByName($group_name){
        $this->join('{users}_groups_members', 'm', "m.user_id = i.id");
        return $this->join('{users}_groups', 'g', "g.id = m.group_id AND g.name = '{$group_name}'");
    }

    public function getUsers(){

        $this->useCache('users.list');

        $this->select("IFNULL(c.name, '')", 'city_name');
        $this->select("IFNULL(c.id, 0)", 'city_id');
        $this->joinLeft('geo_cities', 'c', 'c.id = i.city');

        return $this->get('{users}', function($user){

            $user['groups'] = cmsModel::yamlToArray($user['groups']);
            $user['theme'] = cmsModel::yamlToArray($user['theme']);

            $user['city'] = $user['city_id'] ? array(
                'id' => $user['city_id'],
                'name' => $user['city_name'],
            ) : false;

            return $user;

        });

    }

//============================================================================//
//============================================================================//

    public function getUser($id=false){

        $this->useCache("users.user.{$id}");

        $this->select("IFNULL(c.name, '')", 'city_name');
        $this->select("IFNULL(c.id, 0)", 'city_id');
        $this->joinLeft('geo_cities', 'c', 'c.id = i.city');

        if ($id){
            $user = $this->getItemById('{users}', $id);
        } else {
            $user = $this->getItem('{users}');
        }

        if (!$user) { return false; }

        $user['groups'] = cmsModel::yamlToArray($user['groups']);
        $user['theme'] = cmsModel::yamlToArray($user['theme']);
        $user['notify_options'] = cmsModel::yamlToArray($user['notify_options']);
        $user['privacy_options'] = cmsModel::yamlToArray($user['privacy_options']);

        $user['city'] = $user['city_id'] ? array(
            'id' => $user['city_id'],
            'name' => $user['city_name'],
        ) : false;

        return $user;

    }

    public function getUserByEmail($email){

        $user = $this->filterEqual('email', $email)->getUser();

        return $user;

    }

//============================================================================//
//============================================================================//

    public function getUserByPassToken($pass_token){

        $user = $this->filterEqual('pass_token', $pass_token)->getUser();

        return $user;

    }

    public function clearUserPassToken($id){

        return $this->updateUserPassToken($id, null);

    }

    public function updateUserPassToken($id, $pass_token=null){

        return $this->
                    filterEqual('id', $id)->
                    updateFiltered('{users}', array(
                        'pass_token' => $pass_token,
                        'date_token' => ''
                    ));

    }

//============================================================================//
//============================================================================//

    public function addUser($user){

        $errors = false;

        if ($user['password1'] != $user['password2']){
            $errors['password1'] = LANG_REG_PASS_NOT_EQUAL;
            $errors['password2'] = LANG_REG_PASS_NOT_EQUAL;
            return array( 'success'=>false, 'errors'=>$errors );
        }

        $date_reg = date('Y-m-d H:i:s');
        $date_log = $date_reg;

        $password = $user['password1'];
        $password_salt = md5(implode(':', array($password, session_id(), time(), rand(0, 10000))));
        $password_salt = substr($password_salt, rand(1,8), 16);
        $password_hash = md5(md5($password) . $password_salt);

        $groups = !empty($user['groups']) ? $user['groups'] : array(DEF_GROUP_ID);

        if (isset($user['group_id'])) {
            $groups[] = $user['group_id'];
        }

        unset($user['password1']);
        unset($user['password2']);
        unset($user['group_id']);

        $user = array_merge($user, array(
            'groups' => $groups,
            'password' => $password_hash,
            'password_salt' => $password_salt,
            'date_reg' => $date_reg,
            'date_log' => $date_log,
        ));

        $id = $this->insert('{users}', $user);

        if ($id){

            $this->saveUserGroupsMembership($id, $groups);

            cmsCore::getController('activity')->addEntry('users', "signup", array(
                'user_id' => $id
            ));

        }

        cmsCache::getInstance()->clean("users.list");

        return array(
            'success' => $id!==false,
            'errors' => false,
            'id' => $id
        );

    }

//============================================================================//
//============================================================================//

    public function updateUser($id, $user){

        $success    = false;
        $errors     = false;

        if (!empty($user['email'])){

            $email_exists_id = $this->db->getField('{users}', "email = '{$user['email']}'", 'id');

            if ($email_exists_id && ($email_exists_id != $id)){
                $errors['email'] = LANG_REG_EMAIL_EXISTS;
            }

        }

        if (!empty($user['password1']) && !$errors){

            if (strlen($user['password1']) < 6) {
                $errors['password1'] = ERR_VALIDATE_MIN_LENGTH;
            }

            if ($user['password1'] != $user['password2']){
                $errors['password2'] = LANG_REG_PASS_NOT_EQUAL;
            }

            $password = $user['password1'];
            $password_salt = md5(implode(':', array($password, session_id(), time(), rand(0, 10000))));
            $password_salt = substr($password_salt, rand(1,8), 16);
            $password_hash = md5(md5($password) . $password_salt);

            $user['password'] = $password_hash;
            $user['password_salt'] = $password_salt;

        }

        if (!$errors){

            $user['groups'] = !empty($user['groups']) ? $user['groups'] : array(DEF_GROUP_ID);

            unset($user['password1']);
            unset($user['password2']);
            unset($user['city_id']);
            unset($user['city_name']);
            unset($user['status']);
            unset($user['is_can_vote_karma']);

            $success = $this->update('{users}', $id, $user);

            $this->saveUserGroupsMembership($id, $user['groups']);

        }

        cmsCache::getInstance()->clean("users.list");
        cmsCache::getInstance()->clean("users.user.{$id}");

        return array(
            'success' => $success,
            'errors' => $errors,
            'id' => $id
        );

    }

//============================================================================//
//============================================================================//

    public function deleteUser($id){

        $this->delete('{users}_friends', $id, "user_id");
        $this->delete('{users}_friends', $id, "friend_id");

        $this->delete('{users}_groups_members', $id, "user_id");

        $this->delete('{users}', $id);

        cmsCache::getInstance()->clean("users.list");
        cmsCache::getInstance()->clean("users.user.{$id}");

    }

//============================================================================//
//============================================================================//

    public function unlockUser($id){
        $this->update('{users}', $id, array(
            'is_locked' => false,
            'lock_until' => null,
            'lock_reason' => null
        ));
        cmsCache::getInstance()->clean("users.user.{$id}");
    }

//============================================================================//
//============================================================================//

    public function saveUserGroupsMembership($id, $groups){

        $this->delete('{users}_groups_members', $id, 'user_id');

        foreach($groups as $group_id){
            $this->insert('{users}_groups_members', array(
                'user_id' => $id,
                'group_id' => $group_id
            ));
        }

        cmsCache::getInstance()->clean("users.list");

    }

//============================================================================//
//=========================    УВЕДОМЛЕНИЯ   =================================//
//============================================================================//

    public function getUserNotifyOptions($id){

        return $this->getItemById('{users}', $id, function($item, $model){
            return cmsModel::yamlToArray($item['notify_options']);
        });

    }

    public function updateUserNotifyOptions($id, $options){

        return $this->update('{users}', $id, array('notify_options'=>$options));

    }

    public function getNotifiedUsers($notice_type, $id_list, $options_only=array()){

        $list = array();

        $this->filterIn('id', $id_list);

        $users = $this->get('{users}', function($user, $model){

            return array(
                'id' => $user['id'],
                'email' => $user['email'],
                'nickname' => $user['nickname'],
                'notify_options' => cmsModel::yamlToArray($user['notify_options'])
            );

        });

        if (!$users) { return false; }

        foreach($users as $user){

            if ($options_only){

                if (empty($user['notify_options'][$notice_type])){
                    $user['notify_options'][$notice_type] = 'email';
                }

                if (!in_array($user['notify_options'][$notice_type], $options_only)){
                    continue;
                }

            }

            unset($user['notify_options']);
            $list[] = $user;

        }

        return $list ? $list : false;

    }


//============================================================================//
//=========================    ПРИВАТНОСТЬ   =================================//
//============================================================================//

    public function getUserPrivacyOptions($id){

        return $this->getItemById('{users}', $id, function($item, $model){
            return cmsModel::yamlToArray($item['privacy_options']);
        });

    }

    public function updateUserPrivacyOptions($id, $options){

        return $this->update('{users}', $id, array('privacy_options'=>$options));

    }

//============================================================================//
//==============================    ГРУППЫ   =================================//
//============================================================================//

    public function getGroups($is_guests = false){

        if (!$is_guests) { $this->filterNotEqual('id', 1); }

        return $this->get('{users}_groups');

    }

    public function getPublicGroups(){

        return $this->filterNotEqual('id', 1)->
                        filterEqual('is_public', 1)->
                        get('{users}_groups');

    }

    public function getFilteredGroups(){

        return $this->filterNotEqual('id', 1)->
                        filterEqual('is_filter', 1)->
                        get('{users}_groups');

    }

    public function getGroup($id=false){

        return $this->getItemById('{users}_groups', $id);

    }

    public function updateGroup($id, $group){

        return $this->update('{users}_groups', $id, $group);

    }

    public function addGroup($group){

        return $this->insert('{users}_groups', $group);

    }

    public function deleteGroup($id){

        $this->join('{users}_groups_members', 'm', "m.user_id = i.id AND m.group_id = '{$id}'");

        $members = $this->getUsers();

        if ($members){

            foreach($members as $user){

                $groups = $user['groups'];

                // удаляем ID из массива групп пользователя
                // и переиндексируем ключи массива
                $groups = array_values( array_diff($groups, array($id)) );

                $this->update('{users}', $user['id'], array(
                    'groups' => $groups
                ));

            }

            $this->delete('{users}_groups_members', $id, "group_id");

        }

        $this->delete('{users}_groups', $id);

        return true;

    }

//============================================================================//
//==============================    ДРУЖБА   =================================//
//============================================================================//

    public function filterFriends($user_id){
        $user_id = intval($user_id);
        $this->joinInner('{users}_friends', 'f', "friend_id = i.id AND f.is_mutual = 1 AND f.user_id = '{$user_id}'");
        return $this;
    }

    public function getFriends($user_id){

        $this->useCache('users.friends');

        $this->select('u.id', 'id');
        $this->select('u.*');

        $this->joinInner('{users}', 'u', 'u.id = i.friend_id');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        if (!$this->order_by){
            $this->orderBy('u.date_log', 'desc');
        }

        return $this->get('{users}_friends');

    }


    public function getFriendsCount($user_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        $count = $this->getCount('{users}_friends');

        $this->resetFilters();

        return $count;
    }


    public function getFriendsIds($user_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('is_mutual', 1);

        return $this->get('{users}_friends', function($item, $model){

            return $item['friend_id'];

        }, false);

    }

    public function isFriendshipRequested($user_id, $friend_id){

        $this->useCache('users.friends');

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->filterEqual('is_mutual', 0);

        $is_exists = (bool)$this->getCount('{users}_friends');

        $this->resetFilters();

        return $is_exists;

    }

    public function isFriendshipExists($user_id, $friend_id){

        $this->useCache('users.friends');

        $this->filterStart();
        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->filterEnd();

        $this->filterOr();

        $this->filterStart();
        $this->filterEqual('user_id', $friend_id);
        $this->filterEqual('friend_id', $user_id);
        $this->filterEnd();

        $is_exists = (bool)$this->getCount('{users}_friends');

        $this->resetFilters();

        return $is_exists;

    }


    public function isFriendshipMutual($user_id, $friend_id){

        $this->useCache('users.friends');

        $this->filterStart();
            $this->filterStart();
            $this->filterEqual('user_id', $user_id);
            $this->filterEqual('friend_id', $friend_id);
            $this->filterEnd();

            $this->filterOr();

            $this->filterStart();
            $this->filterEqual('user_id', $friend_id);
            $this->filterEqual('friend_id', $user_id);
            $this->filterEnd();
        $this->filterEnd();

        $this->filterAnd();

        $this->filterEqual('is_mutual', 1);

        $is_exists = (bool)$this->getCount('{users}_friends');

        $this->resetFilters();

        return $is_exists;

    }


    public function addFriendship($user_id, $friend_id){

        $is_mutual = false;

        if ($this->isFriendshipRequested($friend_id, $user_id)){

            $this->filterEqual('user_id', $friend_id);
            $this->filterEqual('friend_id', $user_id);

            $this->updateFiltered('{users}_friends', array(
                'is_mutual' => true
            ));

            $is_mutual = true;

        }

        if ($is_mutual){

            $this->filterEqual('id', $user_id)->increment('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->increment('{users}', 'friends_count');

            $friend = $this->getUser($friend_id);

            cmsCore::getController('activity')->addEntry('users', "friendship", array(
                'subject_title' => $friend['nickname'],
                'subject_id' => $friend_id,
                'subject_url' => href_to('users', $friend_id),
            ));

        }

        cmsCache::getInstance()->clean("users.friends");

        return $this->insert('{users}_friends', array(
            'user_id' => $user_id,
            'friend_id' => $friend_id,
            'is_mutual' => $is_mutual
        ));

    }

    public function deleteFriendship($user_id, $friend_id){

        if ($this->isFriendshipMutual($user_id, $friend_id)){
            $this->filterEqual('id', $user_id)->decrement('{users}', 'friends_count');
            $this->filterEqual('id', $friend_id)->decrement('{users}', 'friends_count');
        }

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('friend_id', $friend_id);
        $this->deleteFiltered('{users}_friends');

        $this->filterEqual('user_id', $friend_id);
        $this->filterEqual('friend_id', $user_id);
        $this->deleteFiltered('{users}_friends');

        cmsCache::getInstance()->clean("users.friends");

    }

//============================================================================//
//=========================    ВКЛАДКИ ПРОФИЛЕЙ   ============================//
//============================================================================//

    public function getUsersProfilesTabs($only_active=false, $by_field='id'){

        $this->useCache('users.tabs');

        if ($only_active){ $this->filterEqual('is_active', 1); }

        return $this->orderBy('ordering')->get('{users}_tabs', false, $by_field);

    }

    public function getUsersProfilesTab($tab_id){

        $this->useCache('users.tabs');

        return $this->getItemById('{users}_tabs', $tab_id);

    }

    public function updateUsersProfilesTab($id, $tab){

        cmsCache::getInstance()->clean("users.tabs");

        return $this->update('{users}_tabs', $id, $tab);

    }

    public function reorderUsersProfilesTabs($fields_ids_list){

        $this->reorderByList('{users}_tabs', $fields_ids_list);

        cmsCache::getInstance()->clean("users.tabs");

        return true;

    }


//============================================================================//
//==============================    СТАТУСЫ   ================================//
//============================================================================//

    public function getUserStatus($id){

        if (!$id) { return false; }

        $this->useCache('users.status');

        return $this->getItemById('{users}_statuses', $id);

    }

    public function addUserStatus($status){

        $id = $this->insert('{users}_statuses', $status);

        $this->update('{users}', $status['user_id'], array(
            'status_text' => $status['content'],
            'status_id' => $id
        ));

        cmsCache::getInstance()->clean("users.status");

        return $id;

    }

    public function clearUserStatus($user_id){

        cmsCache::getInstance()->clean("users.status");
        cmsCache::getInstance()->clean("users.list");
        cmsCache::getInstance()->clean("users.user.{$user_id}");

        $this->update('{users}', $user_id, array(
            'status_text' => null,
            'status_id' => null
        ));

    }

    public function increaseUserStatusRepliesCount($status_id){

        cmsCache::getInstance()->clean("users.status");

        $this->filterEqual('id', $status_id)->increment('{users}_statuses', 'replies_count');

    }

//============================================================================//
//============================    РЕПУТАЦИЯ   ================================//
//============================================================================//

    public function isUserCanVoteKarma($user_id, $profile_id, $voting_days=1){

        $this->filterEqual('user_id', $user_id);
        $this->filterEqual('profile_id', $profile_id);
        $this->filterDateYounger('date_pub', $voting_days);

        $this->useCache("users.karma");

        $votes_count = $this->getCount('{users}_karma');

        $this->resetFilters();

        return $votes_count > 0 ? false : true;

    }

    public function addKarmaVote($vote){

        cmsCache::getInstance()->clean("users.karma");

        $result = $this->insert('{users}_karma', $vote);

        if (!$result) { return false; }

        $this->
            filterEqual('id', $vote['profile_id'])->
            increment('{users}', 'karma', $vote['points']);

        return $result;

    }

    public function getKarmaLogCount($profile_id){

        $this->useCache('users.karma');

        $count = $this->filterEqual('profile_id', $profile_id)->getCount('{users}_karma');

        $this->resetFilters();

        return $count;

    }

    public function getKarmaLog($profile_id){

        $this->useCache('users.karma');

        $this->joinUser();

        $this->orderBy('id', 'desc');

        $this->filterEqual('profile_id', $profile_id);

        return $this->get('{users}_karma', function($item, $model){

            $item['user'] = array(
                'id' => $item['user_id'],
                'nickname' => $item['user_nickname'],
                'avatar' => $item['user_avatar']
            );

            return $item;

        });

    }

//============================================================================//
//============================================================================//

    public function updateUserRating($user_id, $score){

        $this->
            filterEqual('id', $user_id)->
            increment('{users}', 'rating', $score);

        cmsCache::getInstance()->clean("users.list");
        cmsCache::getInstance()->clean("users.user.{$user_id}");

    }

//============================================================================//
//============================================================================//

    public function getMigrationRulesCount(){

        return $this->getCount('{users}_groups_migration');

    }

    public function getMigrationRules(){

        return $this->get('{users}_groups_migration');

    }

    public function getMigrationRule($id){

        return $this->getItemById('{users}_groups_migration', $id);

    }

    public function addMigrationRule($rule){

        return $this->insert('{users}_groups_migration', $rule);

    }

    public function updateMigrationRule($id, $rule){

        return $this->update('{users}_groups_migration', $id, $rule);

    }

    public function deleteMigrationRule($id){

        return $this->delete('{users}_groups_migration', $id);

    }

//============================================================================//
//============================================================================//


}
