<?php

/**
 * dmGeshiMarkdownPlugin configuration.
 * 
 * @package     dmGeshiMarkdownPlugin
 * @subpackage  config
 * @author      Your name here
 * @version     SVN: $Id: PluginConfiguration.class.php 17207 2009-04-10 15:36:26Z Kris.Wallsmith $
 */
class dmGeshiMarkdownPluginConfiguration extends sfPluginConfiguration
{
  const VERSION = '1.0.0-DEV';

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('dm.context.loaded', array($this, 'listenToContextLoadedEvent'));
  }

  public function listenToContextLoadedEvent(sfEvent $e) {
      $e->getSubject()->getResponse()->addStylesheet('dmGeshiMarkdownPlugin.style', 'last');
  }
}
