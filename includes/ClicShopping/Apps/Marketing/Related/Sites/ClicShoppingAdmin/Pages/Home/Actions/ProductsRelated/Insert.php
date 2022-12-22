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

  class Insert extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function execute()
    {

      $CLICSHOPPING_Related = Registry::get('Related');
      $CLICSHOPPING_Db = Registry::get('Db');

      $products_related_id_master = isset($_POST['products_related_id_master']) ? HTML::sanitize($_POST['products_related_id_master']) : '';
      $products_related_id_slave = isset($_POST['products_related_id_slave']) ? HTML::sanitize($_POST['products_related_id_slave']) : '';
      $products_related_sort_order = isset($_POST['products_related_sort_order']) ? HTML::sanitize($_POST['products_related_sort_order']) : '';
      $products_cross_sell = isset($_POST['products_cross_sell']) ? HTML::sanitize($_POST['products_cross_sell']) : '';
      $products_related = isset($_POST['products_related']) ? HTML::sanitize($_POST['products_related']) : '';
      $products_mode_b2b = isset($_POST['products_mode_b2b']) ? HTML::sanitize($_POST['products_mode_b2b']) : '';

      $page_info = null;

      if (isset($_POST['attribute_page'])) $page_info .= 'attribute_page=' . $_POST['attribute_page'] . '&';

      if (!is_null($page_info)) {
        $page_info = substr($page_info, 0, -1);
      }

      if ($products_related_id_master) {
        $products_id_view = $products_related_id_master;
      }

      if ($products_related_id_master != $products_related_id_slave) {

        $Qcheck = $CLICSHOPPING_Db->prepare('select products_related_id
                                       from :table_products_related
                                       where products_related_id_master = :products_related_id_master
                                       and products_related_id_slave = :products_related_id_slave
                                      ');

        $Qcheck->bindInt(':products_related_id_master', (int)$products_related_id_master);
        $Qcheck->bindInt(':products_related_id_slave', (int)$products_related_id_slave);
        $Qcheck->execute();

        if ($Qcheck->fetch() === false) {

          $CLICSHOPPING_Db->save('products_related', ['products_related_id_master' => (int)$products_related_id_master,
              'products_related_id_slave' => (int)$products_related_id_slave,
              'products_related_sort_order' => (int)$products_related_sort_order,
              'products_cross_sell' => (int)$products_cross_sell,
              'products_related' => (int)$products_related,
              'products_mode_b2b' => (int)$products_mode_b2b
            ]
          );
        }
      }

      $CLICSHOPPING_Related->redirect('ProductsRelated&' . $page_info . '&products_id_master=&products_related_id_master=' . $products_related_id_master . '&products_related_id_slave=' . $products_related_id_slave . '&products_id_view=' . $products_id_view);
    }
  }