<?php

namespace ERD\PHPTypographyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ERDPHPTypographyExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        //prep config data
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        //load services file for parameters and other services
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        //add service definition manually based on config
        $service = new Definition('%erd_php_typography.class%');
        foreach($config as $key=>$value) 
        { 
            if($key !== 'use_doctrine_events') { $service->addMethodCall('set_'.$key, array($value)); }
        }
        $container->setDefinition('erd_php_typography', $service);
        
        //unfortunately, the class cant be compiled (i.e. moved with other classes to a single file 
        //for faster loading) because phpTypography tries to include other files from within the class
        //and it expects those files to be in a certain place relative to it. 
        //$this->addClassesToCompile(array('%erd_php_typography.class%'));
        
        //if we're hooking into doctrine, register a service to do so.
        if($config['use_doctrine_events'])
        {
            $subscriber = new Definition($container->getParameter('erd_php_typography.doctrine_subscriber.class'));
            $subscriber->addTag('doctrine.event_subscriber');
            $subscriber->setArguments(array(new Reference('erd_php_typography.applier')));
            $subscriber->setPublic(false);
            
            $container->setDefinition('erd_php_typography.doctrine_subscriber', $subscriber);
        }
    }
    
    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'erd_php_typography';
    }
}