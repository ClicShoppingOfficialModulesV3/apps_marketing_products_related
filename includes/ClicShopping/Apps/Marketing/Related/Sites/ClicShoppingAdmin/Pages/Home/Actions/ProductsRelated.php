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

  namespace ClicShopping\Apps\Marketing\Related\Sites\ClicShoppingAdmin\Pages\Home\Actions;

  use ClicShopping\OM\Registry;

  class ProductsRelated extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {
      $CLICSHOPPING_Related = Registry::get('Related');

      $this->page->setFile('products_related.php');
      $this->page->data['action'] = 'ProductsRelated';

      $CLICSHOPPING_Related->loadDefinitions('Sites/ClicShoppingAdmin/ProductsRelated');
    }
  }