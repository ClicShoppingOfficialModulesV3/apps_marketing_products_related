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

  class RemoveProduct implements \ClicShopping\OM\Modules\HooksInterface
  {
    protected $app;

    public function __construct()
    {
      if (!Registry::exists('Related')) {
        Registry::set('Related', new RelatedApp());
      }

      $this->app = Registry::get('Related');
    }

    private function removeProducts($id)
    {
// products related master
      $QproductsMasterRelated = $this->app->db->prepare('select products_related_id
                                                        from :table_products_related
                                                        where products_related_id_master = :products_related_id_master
                                                      ');
      $QproductsMasterRelated->bindInt(':products_related_id_master', (int)$id);

      $QproductsMasterRelated->execute();

      while ($QproductsMasterRelated->fetch()) {
        $Qdelete = $this->app->db->prepare('delete
                                            from :table_products_related
                                            where products_related_id = :products_related_id
                                          ');
        $Qdelete->bindInt(':products_related_id', $QproductsMasterRelated->valueInt('products_related_id'));
        $Qdelete->execute();
      }

// products related slave
      $QproductsRelatedSlave = $this->app->db->prepare('select products_related_id
                                                        from :table_products_related
                                                        where products_related_id_slave = :products_related_id_slave
                                                      ');
      $QproductsRelatedSlave->bindInt(':products_related_id_slave', (int)$id);

      $QproductsRelatedSlave->execute();

      while ($QproductsRelatedSlave->fetch()) {
        $Qdelete = $this->app->db->prepare('delete
                                           from :table_products_related
                                           where products_related_id = :products_related_id
                                          ');
        $Qdelete->bindInt(':products_related_id', $QproductsRelatedSlave->valueInt('products_related_id'));
        $Qdelete->execute();
      }
    }

    public function execute()
    {
      if (isset($_POST['remove_id'])) $pID = $_POST['remove_id'];
      if (isset($_POST['pID'])) $pID = $_POST['pID'];

      if (isset($pID)) {
        $id = HTML::sanitize($pID);
        $this->removeProducts($id);

        Cache::clear('products_related');
        Cache::clear('products_cross_sell');
      }
    }
  }