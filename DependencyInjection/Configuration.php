<?php

namespace Oryzone\Bundle\ImageResizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Oryzone\Bundle\ImageResizerBundle\Image\ImageFormat;

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

	    $supportedResizeModes = ImageFormat::$RESIZE_MODES;

	    /**
	     * @var Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode
	     */
        $rootNode = $treeBuilder->root('oryzone_image_resizer');

	    /*
	    oryzone_image_resizer:
		    formats:
	            group1:
				    - { name: big,      width: 800,     resizeMode : proportional }
				    - { name: default,  width: 500,     resizeMode: proportional }
				    - { name: medium,   width: 300,     resizeMode: proportional }
				    - { name: small,    width: 100,     resizeMode: crop }
	            group2:
	                - { name: default,  width: 500,     resizeMode: proportional }
				    - { name: medium,   width: 300,     resizeMode: proportional }
	     */




        $rootNode
            ->children()
                ->scalarNode('temp_folder')->defaultNull()->end()
            ->end()
            ->children()
                ->arrayNode('formats')
			        ->useAttributeAsKey('id')
			        ->prototype('array')
			            ->performNoDeepMerging()
	                    ->prototype('array')
	                        ->children()
								->scalarNode('name')->end()
		                        ->scalarNode('width')->defaultNull()->end()
		                        ->scalarNode('height')->defaultNull()->end()
		                        ->scalarNode('resizeMode')->defaultNull()->end()
	                            ->scalarNode('format')
		                            ->defaultValue($supportedResizeModes[0])
							        ->validate()
							            ->ifNotInArray($supportedResizeModes)
							            ->thenInvalid('The resize mode \'%s\' is not supported. Please choose one of '.json_encode($supportedResizeModes))
							        ->end()
		                        ->end()
	                            ->scalarNode('quality')->defaultNull()->end()
	                        ->end()
	                    ->end()
                    ->end()
	            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
