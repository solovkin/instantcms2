<?php

class actionAdminWidgetsReorder extends cmsAction {

    public function run(){

        $position = $this->request->get('position');
        $items = $this->request->get('items');

        if (!$items){ cmsCore::error404(); }

        $widgets_model = cmsCore::getModel('widgets');

        $widgets_model->reorderWidgetsBindings($position, $items);

        $this->halt();

    }

}
