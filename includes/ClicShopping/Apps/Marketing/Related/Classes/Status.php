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

  namespace ClicShopping\Apps\Marketing\Related\Classes;

  use ClicShopping\OM\Registry;

  class Status
  {

    /**
     * Sets the status of a related products
     *
     * @param string $products_related_id_master , $products_related
     * @return string , the status of related products
     * @access public
     */
    Public static function GetProductsRelatedStatus(int $products_related_id_master, int $products_related, int $products_related_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($products_related == 1) {

        return $CLICSHOPPING_Db->save('products_related', ['products_related' => 1],
          ['products_related_id_master' => (int)$products_related_id_master,
            'products_related_id' => (int)$products_related_id
          ]
        );

      } elseif ($products_related == '0') {

        return $CLICSHOPPING_Db->save('products_related', ['products_related' => 0],
          ['products_related_id_master' => (int)$products_related_id_master,
            'products_related_id' => (int)$products_related_id
          ]
        );

      } else {
        return -1;
      }
    }

    /**
     *  Sets the B2B status of a related products cross sell
     *
     * @param string $products_related_id_master , $products_cross_sell, $flag_b2b
     * @return string , the status of B2B status
     * @access public
     */
    Public static function GetProductsModeB2bStatus(int $products_related_id_master, int $flag_b2b, int $products_related_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      if ($flag_b2b == 1) {
        return $CLICSHOPPING_Db->save('products_related', ['products_mode_b2b' => 1],
          ['products_related_id_master' => (int)$products_related_id_master,
            'products_related_id' => (int)$products_related_id
          ]
        );
      } elseif ($flag_b2b == 0) {
        return $CLICSHOPPING_Db->save('products_related', ['products_mode_b2b' => 0],
          ['products_related_id_master' => (int)$products_related_id_master,
            'products_related_id' => (int)$products_related_id
          ]
        );
      } else {
        return -1;
      }
    }


    /**
     *  Sets the status of a related products cross sell
     *
     * @param string $products_related_id_master , $products_cross_sell
     * @return string , the status of cross sell status
     * @access public
     *
     */
    Public static function GetProductsCrossSellStatus(int $products_related_id_master, int $products_cross_sell, int $products_related_id)
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      if ($products_cross_sell == 1) {

        return $CLICSHOPPING_Db->save('products_related', ['products_cross_sell' => 1],
          ['products_related_id_master' => (int)$products_related_id_master,
            'products_related_id' => (int)$products_related_id
          ]
        );

      } elseif ($products_cross_sell == 0) {

        return $CLICSHOPPING_Db->save('products_related', ['products_cross_sell' => 0],
          ['products_related_id_master' => (int)$products_related_id_master,
            'products_related_id' => (int)$products_related_id
          ]
        );

      } else {
        return -1;
      }
    }
  }