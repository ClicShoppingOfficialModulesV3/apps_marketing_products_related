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

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Related = Registry::get('Related');
      $CLICSHOPPING_Db = Registry::get('Db');

      if (!isset($_GET['page']) || !is_numeric($_GET['page'])) {
        $_GET['page'] = 1;
      }

      $products_related_id = isset($_POST['products_related_id']) ? HTML::sanitize($_POST['products_related_id']) : '';
      $products_related_id_master = isset($_POST['products_related_id_master']) ? HTML::sanitize($_POST['products_related_id_master']) : '';
      $products_related_id_slave = isset($_POST['products_related_id_slave']) ? HTML::sanitize($_POST['products_related_id_slave']) : '';
      $products_related_sort_order = isset($_POST['products_related_sort_order']) ? HTML::sanitize($_POST['products_related_sort_order']) : '';

      if ($_POST['products_cross_sell'] == 'on' || $_POST['products_cross_sell'] == '1') {
        $products_cross_sell = 1;
      }
      if ($_POST['products_related'] == 'on' || $_POST['products_related'] == '1') {
        $products_related = 1;
      }
      if ($_POST['products_mode_b2b'] == 'on' || $_POST['products_mode_b2b'] == '1') {
        $products_mode_b2b = 1;
      }

      $products_related_sort_order = HTML::sanitize($_POST['products_id_view']);

      $page_info = null;

      if (isset($_GET['attribute_page'])) $page_info .= 'attribute_page=' . $_GET['attribute_page'] . '&';

      if (!is_null($page_info)) {
        $page_info = substr($page_info, 0, -1);
      }

      $Qupdate = $CLICSHOPPING_Db->prepare('update :table_products_related
                                        set products_related_id_master = :products_related_id_master,
                                            products_related_id_slave = :products_related_id_slave,
                                            products_related_sort_order = :products_related_sort_order,
                                            products_cross_sell = :products_cross_sell,
                                            products_related = :products_related,
                                            products_mode_b2b = :products_mode_b2b
                                        where products_related_id = :products_related_id
                                      ');
      $Qupdate->bindInt(':products_related_id_master', $products_related_id_master);
      $Qupdate->bindInt(':products_related_id_slave', $products_related_id_slave);
      $Qupdate->bindInt(':products_related_sort_order', $products_related_sort_order);
      $Qupdate->bindInt(':products_cross_sell', $products_cross_sell);
      $Qupdate->bindInt(':products_related', $products_related);
      $Qupdate->bindInt(':products_mode_b2b', $products_mode_b2b);
      $Qupdate->bindInt(':products_related_id', $products_related_id);

      $Qupdate->execute();

      Cache::clear('products_related');

      $CLICSHOPPING_Related->redirect('ProductsRelated&' . $page_info . '&products_id_view=' . $products_id_view);
    }
  }
