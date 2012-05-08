<?php

namespace Oryzone\Bundle\ImageResizerBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder();

	    /**
	     * @var Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
	     */
        $rootNode = $treeBuilder->root('oryzone_image_resizer');

	    /*
	    oryzone_image_resizer:
		    formats:
			    - { name: big,      width: 800,     resizeMode : proportional }
			    - { name: default,  width: 500,     resizeMode: proportional }
			    - { name: medium,   width: 300,     resizeMode: proportional }
			    - { name: small,    width: 100,     resizeMode: crop }
	     */

        $rootNode
            ->children()
                ->arrayNode('formats')
                    ->prototype('array')
                        ->children()
							->scalarNode('name')->end()
	                        ->scalarNode('width')->defaultNull()->end()
	                        ->scalarNode('height')->defaultNull()->end()
	                        ->scalarNode('resizeMode')->defaultNull()->end()
                            ->scalarNode('format')->defaultNull()->end()
                            ->scalarNode('quality')->defaultNull()->end()
                        ->end()
                    ->end()
	            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
