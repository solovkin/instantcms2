<?php

    $this->setPageTitle(LANG_COMMENTS, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, $this->href_to(''));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb(LANG_COMMENTS);

?>

<?php echo $html; ?>
