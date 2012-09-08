<?php

class dmGeshiMarkdown extends dmMarkdown {

  protected
  $cacheManager;

  public function __construct(dmHelper $helper, dmCacheManager $cacheManager, array $options = array()) {
    $this->cacheManager = $cacheManager;

    parent::__construct($helper, $options);
  }

  public function getDefaultOptions() {
    return array_merge(parent::getDefaultOptions(), array(
                'use_cache' => true
            ));
  }

  public function toHtml($text) {
    dmContext::getInstance()->getServiceContainer()->getService('response')
            ->addStylesheet('dmGeshiMarkdownPlugin' . '.style', 'first')
    ;
    return parent::toHtml($text);
  }

  protected function preTransform($text) {
    $text = parent::preTransform($text);

    if (strpos($text, '[/code]')) {
      $text = preg_replace_callback(
              '#\[code\s?(\w*)\]((?:\n|.)*)\n\[/code\]#uU', array($this, 'formatCode'), $text
      );
    }

    return $text;
  }

  protected function formatCode(array $matches) {
    // no language specified
    if (!$matches[1]) {
      $html = '<pre><code>' . $matches[2] . '</code></pre>';

      $html = dmString::str_replace_once("\n", '', $html);

      $html = dmString::str_replace_once('  ', '', $html);

      return $html;
    } else {
      return $this->formatGeshiCode($matches);
    }
  }

  protected function formatGeshiCode(array $matches) {
    $code = $matches[2];
    $language = $matches[1];

    $cacheKey = md5($code . $language);

    if ($this->getOption('use_cache') && $cache = $this->cacheManager->getCache('markdown')->get($cacheKey)) {
      return $cache;
    }

    $code = substr($code, 3);
    $code = html_entity_decode($code);

    require_once(dmOs::join(sfConfig::get('sf_plugins_dir'), 'dmGeshiMarkdownPlugin', 'lib', 'vendor/geshi/geshi.php'));


    $geshi = new GeSHi($code, $language);
    $geshi->enable_classes();

    $html = $geshi->parse_code();
    
    if ($this->getOption('use_cache')) {
      $this->cacheManager->getCache('markdown')->set($cacheKey, $html);
    }

    return $html;
  }

}