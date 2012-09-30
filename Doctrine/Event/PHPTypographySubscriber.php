<?php
namespace ERD\PHPTypographyBundle\Doctrine\Event;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Description of PHPTypographySubscriber
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 17, 2012 Ethan Resnick Design
 */
class PHPTypographySubscriber implements \Doctrine\Common\EventSubscriber
{
    /**
     * @var ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalFieldHandler
     */
    protected $applier;

    public function __construct($applier)
    {
        $this->applier = $applier;
    }
    
    public function getSubscribedEvents()
    {
        return array(Events::prePersist, Events::preUpdate);
    }
    
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $this->applier->apply($eventArgs->getEntity());
    }
    
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->applier->apply($eventArgs->getEntity());
    }    
}