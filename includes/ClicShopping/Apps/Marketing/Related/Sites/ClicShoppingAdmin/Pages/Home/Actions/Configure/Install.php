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

  namespace ClicShopping\Apps\Marketing\Related\Sites\ClicShoppingAdmin\Pages\Home\Actions\Configure;

  use ClicShopping\OM\Registry;

  use ClicShopping\OM\Cache;
  use ClicShopping\OM\CLICSHOPPING;

  class Install extends \ClicShopping\OM\PagesActionsAbstract
  {

    public function execute()
    {

      $CLICSHOPPING_MessageStack = Registry::get('MessageStack');
      $CLICSHOPPING_Related = Registry::get('Related');

      $current_module = $this->page->data['current_module'];

      $CLICSHOPPING_Related->loadDefinitions('Sites/ClicShoppingAdmin/install');

      $m = Registry::get('RelatedAdminConfig' . $current_module);
      $m->install();

      static::installDbMenuAdministration();
      static::installProductsRelatedDb();

      $CLICSHOPPING_MessageStack->add($CLICSHOPPING_Related->getDef('alert_module_install_success'), 'success', 'Related');

      $CLICSHOPPING_Related->redirect('Configure&module=' . $current_module);
    }

    private static function installDbMenuAdministration()
    {
      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Related = Registry::get('Related');
      $CLICSHOPPING_Language = Registry::get('Language');
      $Qcheck = $CLICSHOPPING_Db->get('administrator_menu', 'app_code', ['app_code' => 'app_marketing_related']);

      if ($Qcheck->fetch() === false) {

        $sql_data_array = ['sort_order' => 4,
          'link' => 'index.php?A&Marketing\Related&ProductsRelated',
          'image' => 'products_related.gif',
          'b2b_menu' => 0,
          'access' => 0,
          'app_code' => 'app_marketing_related'
        ];

        $insert_sql_data = ['parent_id' => 5];

        $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

        $CLICSHOPPING_Db->save('administrator_menu', $sql_data_array);

        $id = $CLICSHOPPING_Db->lastInsertId();

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = count($languages); $i < $n; $i++) {

          $language_id = $languages[$i]['id'];

          $sql_data_array = ['label' => $CLICSHOPPING_Related->getDef('title_menu')];

          $insert_sql_data = ['id' => (int)$id,
            'language_id' => (int)$language_id
          ];

          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          $CLICSHOPPING_Db->save('administrator_menu_description', $sql_data_array);

        }

        Cache::clear('menu-administrator');
      }
    }

    private function installProductsRelatedDb()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $Qcheck = $CLICSHOPPING_Db->query('show tables like ":table_products_related"');

      if ($Qcheck->fetch() === false) {
        $sql = <<<EOD
CREATE TABLE :table_products_related (
  products_related_id int(11) NOT NULL,
  products_related_id_master int(11) NOT NULL DEFAULT 0,
  products_related_id_slave int(11) NOT NULL DEFAULT 0,
  products_related_sort_order int(6) NOT NULL DEFAULT 0,
  products_cross_sell tinyint(1) NOT NULL DEFAULT 0,
  products_related  tinyint(1) NOT NULL DEFAULT 0,
  products_mode_b2b tinyint(1) NOT NULL DEFAULT 0,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE :table_products_related ADD PRIMARY KEY (products_related_id);
ALTER TABLE :table_products_related MODIFY `products_related_id` int(11) NOT NULL AUTO_INCREMENT;
EOD;
        $CLICSHOPPING_Db->exec($sql);
      }
    }
  }
