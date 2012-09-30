<?php
namespace ERD\PHPTypographyBundle\Mapping\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * When called with an annotated entity passed in, actually modifies the property values of that
 * entity by applying PHPTypography to them according to the annotation's instructions.
 *
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Mar 28, 2012 Ethan Resnick Design
 */
class ConditionalFieldHandler
{   
    private $annotationClass = 'ERD\\PHPTypographyBundle\\Mapping\\Annotation\\ConditionalField';
    private $reader;
    private $phpTypo;
    
    public function __construct(Reader $reader, \phpTypography $phpTypo)
    {
        $this->reader = $reader;
        $this->phpTypo = $phpTypo;
    }
    
    /**
     * @param Object $object The object without PHPTypography applied to its properties
     * @return Object The modified object, with its relevant properties run through PHPTypography. 
     */
    public function apply($object)
    {
        $reflectionObject = new \ReflectionObject($object);
 
        foreach ($reflectionObject->getProperties() as $reflectionProp) 
        {
            $annotation = $this->reader->getPropertyAnnotation($reflectionProp, $this->annotationClass);

            if (null !== $annotation) 
            {
                $condition = $annotation->conditionalOn;
                
                $reflectionProp->setAccessible(true);
                $propValue = $reflectionProp->getValue($object);

                //check that any conditions which may be present pass
                if($condition!==null && $this->checkCondition($condition, $propValue) !== true)
                {
                    continue;
                }
                
                /** @todo Replace the below with more robust recursion */
                //process the value immediately if its a string
                if(is_string($propValue))
                {
                    $newValue = $this->processStringValue($propValue, $annotation);
                }
                
                //or, if it's an array process each index and set the value the new array.
                else if(is_array($propValue))
                {
                    $newValue = array();
                    foreach($propValue as $k=>$v)
                    {
                        $newValue[$k] = $this->processStringValue($v, $annotation);
                    }
                }
                
                else
                {
                    //probably means the property's empty, but it could also be an object or 
                    //someting. Either way, we don't know how to handle it, so we skip it.
                    continue;
                }

                $reflectionProp->setValue($object, $newValue);
            }
        }
 
        return $object;
    }
    
    /**
     * Check that the field value passes any condition(s) that the annotation may have asked to be met.
     * 
     * The only condition currently supported is the string "not-url", which requires the value to not be a url.
     * 
     * @return boolean True if the condition passes or is null. Throws an exception otherwises.
     * @throws AnnotationException If the requested condition can't be validated. Currenlty only "not-url" is supported.
     */
    protected function checkCondition($conditionalOn, $value)
    {
        if($conditionalOn=='not-url')
        {
            return !preg_match("/^([a-z0-9\+\.-]+:\/\/)?(www\.)?([a-z0-9-_\.]+)(\.ac|\.ad|\.aero|\.ae|\.af|\.ag|\.ai|\.al|\.am|\.an|\.ao|\.aq|\.arpa|\.ar|\.asia|\.as|\.at|\.au|\.aw|\.ax|\.az|\.ba|\.bb|\.bd|\.be|\.bf|\.bg|\.bh|\.biz|\.bi|\.bj|\.bm|\.bn|\.bo|\.br|\.bs|\.bt|\.bv|\.bw|\.by|\.bz|\.cat|\.ca|\.cc|\.cd|\.cf|\.cg|\.ch|\.ci|\.ck|\.cl|\.cm|\.cn|\.coop|\.com|\.co|\.cr|\.cu|\.cv|\.cx|\.cy|\.cz|\.de|\.dj|\.dk|\.dm|\.do|\.dz|\.ec|\.edu|\.ee|\.eg|\.er|\.es|\.et|\.eu|\.fi|\.fj|\.fk|\.fm|\.fo|\.fr|\.ga|\.gb|\.gd|\.ge|\.gf|\.gg|\.gh|\.gi|\.gl|\.gm|\.gn|\.gov|\.gp|\.gq|\.gr|\.gs|\.gt|\.gu|\.gw|\.gy|\.hk|\.hm|\.hn|\.hr|\.ht|\.hu|\.id|\.ie|\.il|\.im|\.info|\.int|\.in|\.io|\.iq|\.ir|\.is|\.it|\.je|\.jm|\.jobs|\.jo|\.jp|\.ke|\.kg|\.kh|\.ki|\.km|\.kn|\.kp|\.kr|\.kw|\.ky|\.kz|\.la|\.lb|\.lc|\.li|\.lk|\.lr|\.ls|\.lt|\.lu|\.lv|\.ly|\.ma|\.mc|\.md|\.me|\.mg|\.mh|\.mil|\.mk|\.ml|\.mm|\.mn|\.mobi|\.mo|\.mp|\.mq|\.mr|\.ms|\.mt|\.museum|\.mu|\.mv|\.mw|\.mx|\.my|\.mz|\.name|\.na|\.nc|\.net|\.ne|\.nf|\.ng|\.ni|\.nl|\.no|\.np|\.nr|\.nu|\.nz|\.om|\.org|\.pa|\.pe|\.pf|\.pg|\.ph|\.pk|\.pl|\.pm|\.pn|\.pro|\.pr|\.ps|\.pt|\.pw|\.py|\.qa|\.re|\.ro|\.rs|\.ru|\.rw|\.sa|\.sb|\.sc|\.sd|\.se|\.sg|\.sh|\.si|\.sj|\.sk|\.sl|\.sm|\.sn|\.so|\.sr|\.st|\.su|\.sv|\.sy|\.sz|\.tc|\.td|\.tel|\.tf|\.tg|\.th|\.tj|\.tk|\.tl|\.tm|\.tn|\.to|\.tp|\.travel|\.tr|\.tt|\.tv|\.tw|\.tz|\.ua|\.ug|\.uk|\.um|\.us|\.uy|\.uz|\.va|\.vc|\.ve|\.vg|\.vi|\.vn|\.vu|\.wf|\.ws|\.ye|\.yt|\.yu|\.za|\.zm|\.zw)+\/?((?>=\/)[a-z0-9\$_\.\+!\*'\(\),\{\}\/\^~\[`\/#%\\;\?:@&=\-\]|]*)?$/i", 
                   trim($value));
        }
                    
        elseif($conditionalOn!==null)
        {
            throw new AnnotationException('The '.$this->annotationClass.' annotation only supports "not-url" as a "conditional_on" option, not "'.$conditionalOn.'"');
        }
        
        return true;
    }
    
    /**
     * @param type $oldValue
     * @param \ERD\PHPTypographyBundle\Mapping\Annotation\ConditionalField $annotation
     * @return type 
     */
    protected function processStringValue($oldValue, $annotation)
    {
        //if the output isn't html, the input isn't either. But PHPTypography always 
        //expects HTML. So encode the input to html, run it through phptypography, and
        //then decode again.
        if($annotation->returnHTML === false) 
        { 
            $oldValue = htmlentities($oldValue, ENT_QUOTES, $annotation->charset);   
        }

        $newValue = $this->phpTypo->process($oldValue);

        if($annotation->returnHTML === false) 
        { 
            $newValue = strip_tags(html_entity_decode($newValue, ENT_QUOTES, $annotation->charset));
        }
        
        return $newValue;
    }
}