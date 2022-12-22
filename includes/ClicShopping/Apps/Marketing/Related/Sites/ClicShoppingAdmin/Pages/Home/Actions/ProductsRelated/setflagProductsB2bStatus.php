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


  namespace ClicShopping\Apps\Marketing\Related\Sites\ClicShoppingAdmin\Pages\Home\Actions\ProductsRelated;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Marketing\Related\Classes\Status;

  class setflagProductsB2bStatus extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Related = Registry::get('Related');

      if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
        $_GET['page'] = 1;
      }

      $action = $_GET['action'] ?? '';

      $page_info = null;

//      if (isset($_GET['attribute_page'])) $page_info .= 'attribute_page=' . $_GET['attribute_page'] . '&';

//      if (!is_null($page_info)) {
//        $page_info = substr($page_info, 0, -1);

      Status::GetProductsModeB2bStatus($_GET['products_related_id_master'], $_GET['flag_b2b'], $_GET['products_related_id']);
      $CLICSHOPPING_Related->redirect('ProductsRelated&', (isset($_GET['page']) ? 'page=' . (int)$_GET['page'] . '&' : '') . 'products_related_master_id=' . $_GET['products_related_id_master'] . '&products_related_id=' . $_GET['products_related_id'] . '&attribute_page=' . $_GET['attribute_page']);
//      }
    }
  }