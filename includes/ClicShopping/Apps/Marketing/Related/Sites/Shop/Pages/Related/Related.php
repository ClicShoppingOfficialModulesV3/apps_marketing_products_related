<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Marketing\Related\Sites\Shop\Pages\Related;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Related\Related as RelatedApp;

  class Related extends \ClicShopping\OM\PagesAbstract
  {
    public mixed $app;

    protected function init()
    {
      global $CLICSHOPPING_Related;

      $CLICSHOPPING_Related = new RelatedApp();
      Registry::set('Related', $CLICSHOPPING_Related);

      $CLICSHOPPING_Related = Registry::get('Related');

      $this->app = $CLICSHOPPING_Related;

      $this->app->loadDefinitions('Sites/Shop/main');
    }
  }
