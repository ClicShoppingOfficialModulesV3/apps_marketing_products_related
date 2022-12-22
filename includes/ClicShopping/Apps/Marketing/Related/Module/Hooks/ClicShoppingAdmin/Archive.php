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

  namespace ClicShopping\Apps\Marketing\Related\Module\Hooks\ClicShoppingAdmin\Products;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Cache;

  use ClicShopping\Apps\Marketing\Related\Related as RelatedApp;

  class Archive implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;
    protected $id;

    public function __construct()
    {
      if (!Registry::exists('Related')) {
        Registry::set('Related', new RelatedApp());
      }

      $this->app = Registry::get('Related');
    }

    private function update($id)
    {
// update the products cross sell and related master
      $Qupdate = $this->app->db->prepare('update :table_products_related
                                          set products_cross_sell = 0,
                                              products_related = 0
                                          where products_related_id_master = :products_related_id_master
                                        ');
      $Qupdate->bindInt(':products_related_id_master', $this->ID);
      $Qupdate->execute();

// update the products cross sell and related salve
      $Qupdate = $this->app->db->prepare('update :table_products_related
                                          set products_cross_sell = 0,
                                              products_related = 0
                                          where products_related_id_slave = :products_related_id_slave
                                        ');
      $Qupdate->bindInt(':products_related_id_slave', $this->ID);
      $Qupdate->execute();
    }

    public function execute()
    {
      $this->ID = HTML::sanitize($_POST['products_id']);

      $this->update($this->ID);

      Cache::clear('products_related');
      Cache::clear('products_cross_sell');
    }

  }