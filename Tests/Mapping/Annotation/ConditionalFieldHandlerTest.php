<?php
namespace ERD\PHPTypographyBundle\Tests\Mapping\Annotation;
use ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalFieldHandler;

class ConditionalFieldHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \ERD\PHPTypographyBundle\Tests\Stubs\AnnotatedEntityStub */
    protected $stubEntity;
    
    public function setUp()
    {
        $this->stubEntity = new \ERD\PHPTypographyBundle\Tests\Stubs\AnnotatedEntityStub();
    }
    
    public function testPHPTypographyRunsForAllProperties()
    {
        $mock = $this->getMock('\phpTypography');
        $mock->expects($this->exactly(5))->method('process');

        $applier = new ConditionalFieldHandler(new \Doctrine\Common\Annotations\AnnotationReader(), $mock);
        $applier->apply($this->stubEntity);
    }
    
    public function testPHPTypographyReturnValuesAreSetInClass()
    {
        $stub = $this->getMock('\phpTypography');
        $stub->expects($this->any())->method('process')->will($this->returnValue('PHP Typography ran'));
        
        $applier = new ConditionalFieldHandler(new \Doctrine\Common\Annotations\AnnotationReader(), $stub);
        $applier->apply($this->stubEntity);        
        
        $this->assertTrue($this->stubEntity->getHTMLTrue() === 'PHP Typography ran' //a private property
                          && $this->stubEntity->getCharsetUTF16() === 'PHP Typography ran'); //a protected
    }
    
    public function testReturnHTMLValueApplied()
    {
        $this->markTestIncomplete();
    }
    
    public function testConditionalOnNotURLApplied()
    {
        $this->markTestIncomplete();
    }
    
    public function testInvalidCondtionalOnThrowsException()
    {
        $this->markTestIncomplete();
    }
}
?>