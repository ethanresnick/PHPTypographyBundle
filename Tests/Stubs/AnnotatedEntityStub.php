<?php
namespace ERD\PHPTypographyBundle\Tests\Stubs;
use ERD\PHPTypographyBundle\Mapping\Annotation as Annotation;

/**
 * Stub with annotations to test the 
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Jun 18, 2012 Ethan Resnick Design
 * 
 * @Annotation\ConditionalField(true) 
 */
class AnnotatedEntityStub
{
    /**
     * @Annotation\ConditionalField(true)
     */
    private $htmlTrue = 'Test String';
    
    /**
     * @Annotation\ConditionalField(false)
     */
    protected $htmlFalse = 'Test String';
  
    /**
     * @Annotation\ConditionalField()
     */
    protected $htmlDefault = 'Test String';
    
    /**
     * @Annotation\ConditionalField(charset="UTF-16") 
     */
    protected $charsetUTF16 = 'Test String';
    
    /**
     * @Annotation\ConditionalField(conditional_on="not-url") 
     */
    protected $conditionalOnNotURL = 'Test String';

    /**
     * @Annotation\ConditionalField(true) 
     */
    public function methodAnnotation() {}
    
    //for our tests
    public function setConditionalOnNotURL($string) { $this->conditionalOnNotURL = $string; }
    public function getHTMLTrue() { return $this->htmlTrue; }
    public function getCharsetUTF16() { return $this->charsetUTF16; }
}

?>