<?php

namespace ERD\PHPTypographyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        //basic variable instantiation
        /** @todo factor out this 'new' for testability */
        $tester = new \phpTypography(true);
        $numberValidator = function($v) { return !ctype_digit($v); };
        $quotesTypeArray = array("doubleCurled", "doubleCurledReversed", "doubleLow9", 
                                 "doubleLow9Reversed", "singleCurled", "singleCurledReversed", 
                                 "singleLow9", "singleLow9Reversed", "doubleGuillemetsFrench", 
                                 "doubleGuillemets", "doubleGuillemetsReversed", 
                                 "singleGuillemets", "singleGuillemetsReversed", "cornerBrackets",
                                 "whiteCornerBracket");


        //do the configuration
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('erd_php_typography');
        $rootNode
            ->children()
                //general
                ->arrayNode('tags_to_ignore')->end()
                ->booleanNode('use_doctrine_events')->defaultValue(false)->end()

                //hyphenation
                ->booleanNode('hyphenation')->defaultValue(false)->end()
                ->booleanNode('hyphenate_title_case')->defaultValue(false)->end()
                ->booleanNode('hyphenate_all_caps')->defaultValue(false)->end()
                ->arrayNode('hyphenation_exceptions')->end()
                ->scalarNode('hyphenation_language')
                    ->validate()
                        ->ifNotInArray($tester->get_languages())
                        ->thenInvalid('PHPTypographyBundle: the %s hyphenation language is not supported')
                    ->end()
                ->end()

                
                //wrapping and dewidow                -
                ->booleanNode('wrap_hard_hyphens')->end()
                ->booleanNode('email_wrap')->end()
                ->booleanNode('url_wrap')->end()
                ->booleanNode('space_​collapse')->end()
                ->booleanNode('dewidow')->defaultValue(true)->end()                
                ->scalarNode('​max_dewidow_length')
                    ->validate()
                        ->ifTrue($numberValidator)
                        ->thenInvalid("PHPTypographyBundle: the value %s is not supported for max_dewidow_length because this value must be a number")
                    ->end()
                ->end()
                ->scalarNode('max_dewidow_pull')
                    ->validate()
                        ->ifTrue($numberValidator)
                        ->thenInvalid("PHPTypographyBundle: the value %s is not supported for max_dewidow_pull because this value must be a number")
                    ->end()
                ->end()
                
                //smart quotes
                ->booleanNode('smart_quotes')->end()
                ->scalarNode('smart_​quotes_​primary')
                    ->validate()
                        ->ifNotInArray($quotesTypeArray)
                        ->thenInvalid('PHPTypographyBundle: the %s primary quote style is not supported')
                    ->end()
                ->end()
                ->scalarNode('smart_quotes_secondary')
                    ->validate()
                        ->ifNotInArray($quotesTypeArray)
                        ->thenInvalid('PHPTypographyBundle: the %s secondary quote style is not supported')
                    ->end()
                ->end()

                //diacratics
                ->scalarNode('diacritic_language')
                    ->validate()
                        ->ifNotInArray($tester->get_diacritic_languages())
                        ->thenInvalid('PHPTypographyBundle: the %s diacratic language is not supported')
                    ->end()
                ->end()

                //math & spacing
                ->booleanNode('fraction_spacing')->end()
                ->booleanNode('unit_spacing')->end()
                ->booleanNode('single_character_word_spacing')->end()
                ->booleanNode('space_collapse')->end()
            ->end();

        return $treeBuilder;
    }
}