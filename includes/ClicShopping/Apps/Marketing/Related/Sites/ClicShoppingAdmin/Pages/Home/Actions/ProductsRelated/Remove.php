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
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  class Remove extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Related = Registry::get('Related');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (isset($_GET['products_related_id'])) {

        $products_id_view = $_GET['products_id_view'];

        $page_info = null;
        if (isset($_POST['attribute_page'])) $page_info .= 'attribute_page=' . $_POST['attribute_page'] . '&';

        if (!is_null($page_info)) {
          $page_info = substr($page_info, 0, -1);
        }

        $products_related_id = HTML::sanitize($_GET['products_related_id']);

        $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                          from :table_products_related
                                          where products_related_id = :products_related_id
                                        ');
        $Qdelete->bindInt(':products_related_id', (int)$products_related_id);
        $Qdelete->execute();

        Cache::clear('products_related');

        $CLICSHOPPING_Related->redirect('ProductsRelated&', $page_info . '&products_id_view=' . $products_id_view);
      }
    }
  }