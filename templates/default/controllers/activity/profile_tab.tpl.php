<?php

    $this->setPageTitle(LANG_USERS_PROFILE_ACTIVITY, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, $this->href_to(''));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb(LANG_USERS_PROFILE_ACTIVITY);

?>

<?php echo $html; ?>
