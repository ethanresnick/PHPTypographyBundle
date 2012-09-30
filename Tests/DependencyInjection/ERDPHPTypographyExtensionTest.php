<?php
namespace ERD\PHPTypographyBundle\Tests\DependencyInjection;
use ERD\PHPTypographyBundle\DependencyInjection\ERDPHPTypographyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Description of DependencyInjection
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 17, 2012 Ethan Resnick Design
 */
class ERDPHPTypographyExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;
    
    /** @var ContainerBuilder */
    protected $container;
    
    public function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->extension = new ERDPHPTypographyExtension();
    }
    
    public function testUseDoctrineOptionRegistersListenerService()
    {
        $this->extension->load(array(array('use_doctrine_events'=>true)), $this->container);
        $this->assertTrue(in_array("erd_php_typography.doctrine_subscriber", $this->container->getServiceIds()));
    }

    public function testUseDoctrineListenerServiceTaggedCorrectly()
    {
        $this->extension->load(array(array('use_doctrine_events'=>true)), $this->container);
        $this->assertTrue($this->container->getDefinition("erd_php_typography.doctrine_subscriber")->hasTag("doctrine.event_subscriber"));
    }
    
    public function testListenerServiceNotRegisteredWithoutDoctrineOption()
    {
        $this->extension->load(array(array('use_doctrine_events'=>false)), $this->container);
        $this->assertFalse(in_array("erd_php_typography.doctrine_subscriber", $this->container->getServiceIds()));
    }
}