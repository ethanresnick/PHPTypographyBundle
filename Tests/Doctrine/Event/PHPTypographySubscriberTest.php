<?php
namespace ERD\PHPTypographyBundle\Tests\Doctrine\Event;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 17, 2012 Ethan Resnick Design
 */
class PHPTypographySubscriberTest extends WebTestCase
{
    protected static $kernel;
    
    private static $container;

    private static $em;

    private static $subscriberClass;
    
    public static function setUpBeforeClass()
    {
        static::$kernel = static::createKernel(); //make a test kernel
        static::$kernel->boot();
        static::$container = static::$kernel->getContainer();
        static::$em = static::$container->get('doctrine.orm.entity_manager');
        static::$subscriberClass = static::$container->getParameter('erd_php_typography.doctrine_subscriber.class');
    }

    public function tearDown()
    {
        /* here so the parent tearDown, which assumes the kernel is setUp() before every test,
         * isn't called after every test; we only need to setUp and teardown the kernel once. 
         */
    }

    public static function tearDownAfterClass()
    {
        static::$em->getConnection()->close();
        static::$em = null;
        static::$container = null;
        static::$kernel->shutdown();
    }


    /**
     * Since we're distributing this bundle to have it mixed in with other people's code, 
     * and they may try to run our tests with god knows what setup, it's too risky to actually
     * create new entity classes or objects or actually do persistence. So we just do a check
     * that doctrine agrees things are registered.
     */
    public function eventsProvider()
    {   
        return array(
          array(Events::preUpdate, '\Doctrine\ORM\Event\PreUpdateEventArgs'),
          array(Events::prePersist, '\Doctrine\ORM\Event\LifecycleEventArgs')
        );
    }

    /**
     * @dataProvider eventsProvider
     */
    public function testSubscriberListensToProperEvents($event, $argsClass)
    {
        //we can't get the service itself (that's private), so we check for a listener of its class.
        foreach(static::$em->getEventManager()->getListeners($event) as $listener)
        {
            if($listener instanceof static::$subscriberClass)
            {
                return true;
            }
        }

        $this->fail('No listener of the proper class is registered for the '.$event.' event.');        
    }
    
    /**
     * @dataProvider eventsProvider 
     */
    public function testPHPTypographyApplierRunsOnProperEvents($event, $argsClass)
    {   
        $applierMock = $this->getMockBuilder('\ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalFieldHandler')->disableOriginalConstructor()->getMock();
        $applierMock->expects($this->once())->method('apply');

        $eventArgsStub = $this->getMockBuilder($argsClass)->disableOriginalConstructor()->getMock();
        $eventArgsStub->expects($this->any())->method('getEntity')->will($this->returnValue(new \stdClass()));

        $subscriber = new static::$subscriberClass($applierMock); 
        
        //dispatch the event manually, just to this subscriber, to see if our mocks/stubs work.
        $subscriber->$event($eventArgsStub);
    }
}