<?php

namespace Application\Controller;

use GoogleGlass\Controller\AbstractSubscriptionController;

class GlassSubscriptionController extends AbstractSubscriptionController
{
    public function onDeleteAction()
    {
        return array('foo' => 'bar', 'baz' => 'hell yeah');
    }
}