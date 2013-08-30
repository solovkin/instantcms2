<?php

    $this->setPageTitle(LANG_GROUPS, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, $this->href_to(''));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb(LANG_GROUPS);

?>

<?php echo $html; ?>
