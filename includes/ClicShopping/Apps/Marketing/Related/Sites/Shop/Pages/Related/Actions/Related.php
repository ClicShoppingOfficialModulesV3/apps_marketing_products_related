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

  namespace ClicShopping\Apps\Marketing\Related\Sites\Shop\Pages\Related\Actions;

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  use ClicShopping\Apps\Marketing\Related\Related as RelatedApp;

  class Related extends \ClicShopping\OM\PagesActionsAbstract
  {


    public $code;
    public $title;
    public $description;
    public $sort_order = 0;

    public function execute()
    {
      global $CLICSHOPPING_Template, $CLICSHOPPING_ProductsCommon, $related, $bootstrap_column;

      $CLICSHOPPING_Db = Registry::get('Db');
      $CLICSHOPPING_Breadcrumb = Registry::get('Breadcrumb');
      $CLICSHOPPING_Language = Registry::get('Language');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_Template = Registry::get('Template');

      if (!Registry::exists('Related')) {
        Registry::set('Related', new RelatedApp());
      }

      $CLICSHOPPING_Related = Registry::get('Related');

      $this->app = $CLICSHOPPING_Related;

      if (CLICSHOPPING_APP_RELATED_RE_STATUS == 'True') {
        if (isset($_GET['Products']) && isset($_GET['Related'])) {

          if (CLICSHOPPING_APP_RELATED_RE_MAX_DISPLAY != 0) {

            $products_template = CLICSHOPPING_APP_RELATED_RE_TEMPLATE;

            if ($CLICSHOPPING_Customer->getCustomersGroupID() != 0) {

              $QRelated = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                  pd.products_name
                                                  from :table_products p  left join :table_products_groups g on p.products_id = g.products_id,
                                                       :table_products_description pd
                                                  where p.products_status = 1
                                                  and g.products_group_view = 1
                                                  and g.customers_group_id = :customers_group_id
                                                  and p.products_ordered > 0
                                                  and p.products_id = pd.products_id
                                                  and pd.language_id = :language_id
                                                  order by p.products_ordered desc,
                                                           pd.products_name
                                                  limit :limit
                                               ');
              $QRelated->bindInt(':language_id', $CLICSHOPPING_Language->getId());
              $QRelated->bindInt(':customers_group_id', $CLICSHOPPING_Customer->getCustomersGroupID());
              $QRelated->bindInt(':limit', CLICSHOPPING_APP_RELATED_RE_MAX_DISPLAY);

              $QRelated->execute();

            } else {

              $QRelated = $CLICSHOPPING_Db->prepare('select distinct p.products_id,
                                                                  pd.products_name
                                                from :table_products p,
                                                     :table_products_description pd
                                                where p.products_status = 1
                                                and p.products_view = 1
                                                and p.products_ordered > 0
                                                and p.products_id = pd.products_id
                                                and pd.language_id = :language_id
                                                order by p.products_ordered desc,
                                                         pd.products_name
                                                limit :limit
                                           ');
              $QRelated->bindInt(':language_id', $CLICSHOPPING_Language->getId());
              $QRelated->bindInt(':limit', CLICSHOPPING_APP_RELATED_RE_MAX_DISPLAY);

              $QRelated->execute();
            }

            $related = $QRelated->fetchAll();

            if (count($related) > 0) {

// Content
              $bootstrap_column = (int)CLICSHOPPING_APP_RELATED_RE_COLUMNS;

//language
              $this->app->loadDefinitions('Sites/Shop/main');

// templates
              $this->page->setFile('best_selling.php');

//Content
//      $this->page->data['current_module'] = (isset($_GET['module']) && in_array($_GET['module'], $modules)) ? $_GET['module'] : $default_module;
//          $this->page->data['content'] = $CLICSHOPPING_Template->getTemplateFiles('best_selling');

              $CLICSHOPPING_Breadcrumb->add($this->app->getDef('navbar_title_1'), CLICSHOPPING::link(null, 'Products&Related'));

            } else {
              echo ' <div class="contentText" style="padding-top:20px;">
                        <div class="alert alert-warning text-center" role="alert">
                           <h3>' . $this->app->getDef('text_product_not_found') . '</h3>
                        </div>
                        <div class="separator"></div>
                        <div class="control-group">
                          <div class="controls">
                            <div class="buttonSet">
                              <span class="text-end">' . HTML::button($this->app->getDef('button_continue'), null, CLICSHOPPING::link(), 'success') . '</span>
                            </div>
                          </div>
                        </div>
                      </div>
                     ';
            }
          } else {
            echo ' <div class="contentText" style="padding-top:20px;">
                    <div class="alert alert-warning text-center" role="alert">
                       <h3>' . $this->app->getDef('text_product_not_found') . '</h3>
                    </div>
                    <div class="separator"></div>
                    <div class="control-group">
                      <div class="controls">
                        <div class="buttonSet">
                          <span class="text-end">' . HTML::button($this->app->getDef('button_continue'), null, CLICSHOPPING::link(), 'success') . '</span>
                        </div>
                      </div>
                    </div>
                  </div>
                ';
          }
        }
      } else {
        CLICSHOPPING::redirect();
      }
    }
  }

