<?php

namespace Canabelle\CMSAnalyticsBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('canabelle_cms_analytics');

        $rootNode
            ->children()
                ->scalarNode('client_id')->defaultValue('cliend-id@developer.gserviceaccount.com')->end()
                ->scalarNode('profile_id')->defaultValue('ga:profile-id')->end()
                ->scalarNode('private_key_file')->defaultValue('%kernel.root_dir%/Resources/bin/google_analytics_private_key.p12')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
