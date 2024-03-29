<?php

class onUsersCronMigration extends cmsAction {

    public function run(){

        $rules = $this->model->filterEqual('is_active', 1)->getMigrationRules();

        foreach ($rules as $rule){

            extract($rule);

            $this->model->filterGroup( $group_from_id );

            $users = $this->model->getUsers();

            if (!$users) { continue; }

            foreach($users as $user){

                $is_migrate = true;

                if ($is_passed){

                    $passed_from = $passed_from ? 'date_group' : 'date_reg';

                    $start_time = strtotime($user[$passed_from]);
                    $end_time = time();

                    $days = round(($end_time - $start_time)/60/24);

                    if ($days < $passed_days){ $is_migrate = false; }

                }

                if ($is_rating){
                    if ($user['rating'] < $rating) { $is_migrate = false; }
                }

                if ($is_karma){
                    if ($user['karma'] < $karma) { $is_migrate = false; }
                }

                if (!$is_migrate) { continue; }

                if (!$is_keep_group){
                    if(($key = array_search($group_from_id, $user['groups'])) !== false) {
                        unset($user['groups'][$key]);
                    }
                }

                $user['groups'][] = $group_to_id;
                $user['groups'] = array_unique($user['groups']);

                $this->model->updateUser($user['id'], array(
                    'groups' => $user['groups'],
                    'date_group' => null
                ));

                if (!$is_notify) { continue; }

                $messenger = cmsCore::getController('messages');

                $messenger->addRecipient($user['id']);

                $messenger->sendNoticePM(array('content' => nl2br($notify_text)));

            }

        }

    }

}
