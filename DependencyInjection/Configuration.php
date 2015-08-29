<?php

namespace ExperientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your
 * app/config files
 **/
class Configuration implements ConfigurationInterface
{
  /**
   * {@inheritDoc}
   */
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('experient')
      ->children()
        ->scalarNode('username')->isRequired()->end()
        ->scalarNode('password')->isRequired()->end()
        ->scalarNode('showcode')->isRequired()->end()
        ->scalarNode('accountDomain')->isRequired()->end()
        ->scalarNode('wsdl')->isRequired()->end()
        ->scalarNode('namespace')->isRequired()->end()
      ->end();

    return $treeBuilder;
  }
}
