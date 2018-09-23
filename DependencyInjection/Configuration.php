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
                ->scalarNode('profile_id')->defaultValue('google-analytics-profile-id')->end()
                # If you don't know your profile ID, please open the Google Analytics website, login, and select the website you want to monitor with PRTG.
                # In your web browser's URL field you will see content similar to this:
                # https://www.google.com/analytics/web/#report/visitors-overview/a5559982w55599512p12345678
                # Please note the structure at the end of the URL:
                # /a[6 digits]w[8 digits]p[8 digits]
                # The 8 digits that follow the "p" are your profile ID. In the example above, this would be 12345678.

                ->scalarNode('private_key_file')->defaultValue('%kernel.root_dir%/Resources/bin/google_analytics_private_key.json')->end()
                # In https://console.cloud.google.com/apis/credentials click to "Create credentials" button
                # Select "Service account key"
                # Choose JSON type
                #
                # In Google Analytics (Admin / Property - User Management) set Read & Analyze permissions to client_email from key file.
            ->end()
        ;

        return $treeBuilder;
    }
}
