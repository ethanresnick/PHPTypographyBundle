<?php
namespace ERD\PHPTypographyBundle\Tests\Mapping\Annotation;
use ERD\PHPTypographyBundle\Tests\Stubs\AnnotatedEntityStub;
use ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalField;
/**
 * Description of ConditionalFieldTest
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 18, 2012 Ethan Resnick Design
 */
class ConditionalFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Doctrine\Common\Annotations\AnnotationReader */
    protected $reader;

    /** @var \ReflectionClass */
    protected $mockReflectionEntity;
    
    /** @var array */
    protected $annotationDefaults;
    
    public function setUp()
    {
        $unconfiguredAnnotation = new ConditionalField(array());
        $this->annotationDefaults = array('returnHTML'=>$unconfiguredAnnotation->returnHTML);
        $this->reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $this->mockReflectionEntity = new \ReflectionClass(get_class(new AnnotatedEntityStub()));
    }

    public function testAnnotationOnlyAllowedOnProperties()
    {
        //we want to try to load annotations on the class and methods and make sure we get
        //exceptions, but we can't do that with Doctrine Common 2.1.x since the TARGET feature
        //wasn't implemented til 2.2, which we're not using yet.
        $this->markTestIncomplete();
    }

    public function annotatedHTMLPropertiesProvider()
    {
        //can't get access to the setUp() stuff here, so remake the object.
        $unconfiguredAnnotation = new ConditionalField(array());

        return array(
            array('htmlTrue', true), 
            array('htmlFalse', false), 
            array('htmlDefault', $unconfiguredAnnotation->returnHTML)
        );
    }

    /**
     * @dataProvider annotatedHTMLPropertiesProvider
     */
    public function testReturnHTMLHonored($propertyName, $value)
    {
        $property = $this->mockReflectionEntity->getProperty($propertyName);
        $annotation = $this->reader->getPropertyAnnotation($property, 'ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalField');
        $this->assertTrue(($annotation->returnHTML===$value)); 
    }
    
    public function testReturnHTMLHasValidDefault()
    {   
        $this->assertTrue(in_array($this->annotationDefaults['returnHTML'], array(true, false)));
    }

    public function testConditionalOnHonored()
    {
        $property = $this->mockReflectionEntity->getProperty('conditionalOnNotURL');
        $annotation = $this->reader->getPropertyAnnotation($property, 'ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalField');
        
        $this->assertTrue(($annotation->conditionalOn==='not-url'));
    }
    
    public function testCharsetHonored()
    {
        $property = $this->mockReflectionEntity->getProperty('charsetUTF16');
        $annotation = $this->reader->getPropertyAnnotation($property, 'ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalField');
        
        $this->assertTrue(($annotation->charset==='UTF-16'));
    }
    
    public function testReturnHTMLGetsDefaultWhenOnlyOtherFieldsSet()
    {
        $property = $this->mockReflectionEntity->getProperty('charsetUTF16');
        $annotation = $this->reader->getPropertyAnnotation($property, 'ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalField');
        
        $this->assertTrue($annotation->returnHTML===$this->annotationDefaults['returnHTML']);
    }
}
?>