<?php

namespace Oryzone\Bundle\ImageResizerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Oryzone\Bundle\ImageResizerBundle\Image\ImageFormat;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OryzoneImageResizerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
	    $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

	    $container->setParameter('oryzone_image_resizer.formats_groups', $config['formats']);
	    $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

	    if(isset($config['temp_folder']) && $config['temp_folder'] != NULL)
		    $container->setParameter('oryzone_image_resizer.temp_folder', $config['temp_folder']);
    }
}