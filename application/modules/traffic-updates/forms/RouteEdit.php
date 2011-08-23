<?php
class TrafficUpdates_Form_RouteEdit extends TrafficUpdates_Form_Route {
    public function init() {
        parent::init();
        $this->getElement('submit')->setLabel('Update');
    }
}
