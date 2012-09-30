<?php
namespace ERD\PHPTypographyBundle\Mapping\Annotation;


/**
 * Runs the marked property through PHPTypography with the applied settings.
 * 
 * Note that this class doesn't actually do the running. That's in ConditionalFieldHandler.php
 * 
 * @author Ethan Resnick Design <hi@ethanresnick.com>
 * @copyright Mar 28, 2012 Ethan Resnick Design
 * 
 * @Annotation
 * @Target({"PROPERTY"})
 */
class ConditionalField
{
    public $returnHTML = true;
    public $charset = 'UTF-8';
    public $conditionalOn = null;

    public function __construct(array $options)
    {
        //must be a legit boolean not just an empty arg because with an empty arg the user might not meant to have
        //set it at all (i.e. just done a @PHPTypography\ConditionalField() with the () producing an empty string.
        if(isset($options['value'])) { $this->returnHTML = (bool) $options['value'];; }
        if(isset($options['charset'])) { $this->charset = (string) $options['charset']; }
        if(isset($options['conditional_on'])) { $this->conditionalOn = $options['conditional_on']; }
    }
}