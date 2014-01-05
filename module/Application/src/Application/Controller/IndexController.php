<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;

class IndexController extends AbstractController
{
    use \Application\Log\LoggerTrait;
    
    public function indexAction()
    {
        $client = $this->getServiceLocator()->get('GoogleGlass\Service\GlassService');
        
        $timelineItems = $client->execute('timeline::list');
        
        /*
        $contact = $this->getServiceLocator()->get('GoogleGlass\Contact');
        
        $contact->setAcceptCommands(array(Contact::COMMAND_POST_UPDATE))
                ->setDisplayName("CoogleWare")
                ->setSpeakableName("Coogle Ware")
                ->setType(Contact::TYPE_GROUP)
                ->insert();
        
        //$glassService->subscribe(GlassService::COLLECTION_TIMELINE);
        $glassService->unsubscribe(GlassService::COLLECTION_TIMELINE);
        */
        
        return new ViewModel(compact('timelineItems'));
    }
}
