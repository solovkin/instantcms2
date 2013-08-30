<?php
class widgetUsersList extends cmsWidget {

    public function run(){

        $dataset = $this->getOption('dataset', 'latest');
        $groups = $this->getOption('groups');
        $is_avatars = $this->getOption('is_avatars');
        $limit = $this->getOption('limit', 10);

        $model = cmsCore::getModel('users');

        switch ($dataset){
            case 'latest': $model->orderBy('date_reg', 'desc'); break;
            case 'rating': $model->orderBy('karma desc, rating desc'); break;
            case 'popular': $model->orderBy('friends_count', 'desc'); break;
        }

        if ($groups){
            $model->filterGroups($groups);
        }

        $profiles = $model->
                        limit($limit)->
                        getUsers();

        if (!$profiles) { return false; }

        return array(
            'profiles' => $profiles,
            'is_avatars' => $is_avatars,
        );

    }

}
