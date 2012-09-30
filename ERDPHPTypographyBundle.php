<?php

namespace ERD\PHPTypographyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ERDPHPTypographyBundle extends Bundle
{
    /**
     * Override symfony's native code for finding the extension because it forces a messy alias 
     * (erdphp_typography instead of erd_php_typography). So below we load the same extension 
     * but disable the alias check and then set the alias propely in the extension code.
     */
    public function getContainerExtension()
    {
        if($this->extension !== null) { return $this->extension; }
        
        $class = $this->getNamespace().'\\DependencyInjection\\'.preg_replace('/Bundle$/', '', $this->getName()).'Extension';
        
        if (class_exists($class)) 
        {
            $extension = new $class();
            $this->extension = $extension;
        } 
        else 
        {
            $this->extension = false;
        }
        
        return $this->extension;
    }
}
