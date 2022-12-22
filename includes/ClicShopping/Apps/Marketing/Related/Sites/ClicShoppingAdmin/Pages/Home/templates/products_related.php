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

  use ClicShopping\OM\HTML;


  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Related = Registry::get('Related');
  $CLICSHOPPING_Page = Registry::get('Site')->getPage();
  $CLICSHOPPING_ProductsAdmin = Registry::get('ProductsAdmin');
  $CLICSHOPPING_Language = Registry::get('Language');
  $CLICSHOPPING_Template = Registry::get('TemplateAdmin');

  $languages = $CLICSHOPPING_Language->getLanguages();

  $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

  $action = $_GET['action'] ?? '';

  $products_id_slave = '';

  $products_id_view = (isset($_GET['products_id_view']) && $_GET['products_id_view'] != 0) ? (int)$_GET['products_id_view'] : null;

  $products_related_id_master = (isset($_GET['products_related_id_master']) && $_GET['products_related_id_master'] != 0) ? (int)$_GET['products_related_id_master'] : null;

  if ($products_related_id_master) {
    $products_id_view = $products_related_id_master;
  }

  if (!is_null($action)) {
    $page_info = null;

    if (isset($_GET['attribute_page'])) $page_info .= 'attribute_page=' . $_GET['attribute_page'] . '&';

    if (!is_null($page_info)) {
      $page_info = substr($page_info, 0, -1);
    }
  }
?>
<div class="contentBody">
  <div class="row">
    <div class="col-md-12">
      <div class="card card-block headerCard">
        <div class="row">
          <span
            class="col-md-1 logoHeading"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'categories/products_related.gif', $CLICSHOPPING_Related->getDef('heading_title_search'), '40', '40'); ?></span>
          <span
            class="col-md-2 pageHeading"><?php echo '&nbsp;' . $CLICSHOPPING_Related->getDef('heading_title_search'); ?></span>
          <span class="col-md-3">
          <div class="controls">
<?php
  echo HTML::form('search', null, 'post', null, ['session_id' => true]);
  echo HTML::inputField('search', '', 'id="inputKeywords" placeholder="' . $CLICSHOPPING_Related->getDef('heading_title_search') . '"');
?>
            </form>
          </div>
         </span>
          <span>
<?php
  echo HTML::form('formview', null, 'post', 'class="form-inline"');
?>
        <select name="products_id_view" onchange="return formview.submit();">
<?php
  echo '<option name="show_all_products" value="">' . $CLICSHOPPING_Related->getDef('show_all_products') . '</option>';

  $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                             p.products_model,
                                             pd.products_name
                                      from :table_products p,
                                           :table_products_description pd
                                      where pd.products_id = p.products_id
                                      and pd.language_id = :language_id
                                      and p.products_archive = :products_archive
                                      order by  p.products_model,
                                                pd.products_name
                                    ');

  $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
  $Qproducts->bindInt(':products_archive', 0);
  $Qproducts->execute();

  while ($Qproducts->fetch()) {

    $model = $Qproducts->value('products_model') . ' - ';
    $name = $Qproducts->value('products_name');

    if ($products_id_view == $Qproducts->valueInt('products_id')) {
      echo '<option name="' . $name . '" value="' . $Qproducts->valueInt('products_id') . '" SELECTED>' . $model . $name . '</option>';
    } else {
      echo '<option name="' . $name . '" value="' . $Qproducts->value('products_id') . '">' . $model . $name . '</option>';
    }
  }
?>
          </select>
            </form>
      </span>

          <span class="col-md-2 text-end">
<?php
  //    if (isset($_POST['search']) && !is_null($_POST['search'])) {
?>
            <span
              class="text-end"><?php echo HTML::button($CLICSHOPPING_Related->getDef('button_reset'), null, $CLICSHOPPING_Related->link('ProductsRelated'), 'warning'); ?></span>
<?php
  //   }
?>
            <?php
              if (!empty($products_related_id) || !empty($_POST['products_related_id_master'])) {
                ?>
                <span
                  class="text-end"><?php echo HTML::button($CLICSHOPPING_Related->getDef('button_cancel'), null, $CLICSHOPPING_Related->link('ProductsRelated'), 'warning'); ?></span>
                <?php
              }
            ?>
          </span>
          <span class="col-md-1 text-end">
            <?php echo HTML::button($CLICSHOPPING_Related->getDef('button_configure'), null, $CLICSHOPPING_Related->link('Configure'), 'primary'); ?>
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="separator"></div>
  <!-- //################################################################################################################ -->
  <!-- //                                      Update Attributes                                       -->
  <!-- //################################################################################################################ -->
  <?php
    if (isset($_GET['Edit'])) {
      $form_action = '&Update';
    } else {
      $form_action = '&add_product_attributes';
    }

    if (!isset($attribute_page)) {
      $attribute_page = 1;
    }

    $form_params = $form_action . '&option_page=' . $option_page . '&value_page=' . $value_page . '&attribute_page=' . $attribute_page;

    $keywords = '';
    $view_id = '';

    $products_id_view = HTML::sanitize($_POST['products_id_view']);

    if (isset($products_id_view)) {
      $products_id_view = $products_related_id_master ? $products_related_id_master : $products_id_view;
      $view_id = '&products_id_view=' . $products_id_view;
    }


    if (isset($_POST['search']) && !is_null($_POST['search'])) {
      $keywords = HTML::sanitize($_POST['search']);

      $Qattributes = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS  pa.products_related_id,
                                                                     pa.products_related_id_master,
                                                                     pa.products_related_id_slave,
                                                                     pa.products_related_sort_order,
                                                                     pa.products_related,
                                                                     pa.products_cross_sell,
                                                                     pa.products_mode_b2b
                                        from :table_products_related pa left join :table_products_description pd ON pa.products_related_id_master = pd.products_id
                                                                        left join :table_products p on pa.products_related_id_master = p.products_id
                                        where (pd.products_name like :keywords
                                               or p.products_model like :keywords
                                              )
                                        and pd.language_id = :language_id

                                        group by pd.products_name,
                                                 pa.products_related_sort_order,
                                                 pa.products_related_id
                                        limit :page_set_offset,
                                              :page_set_max_results

                                        ');

      $Qattributes->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qattributes->bindValue(':keywords', '%' . $keywords . '%');
      $Qattributes->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qattributes->execute();

    } elseif (isset($_GET['products_related_id']) && !is_null($_GET['products_related_id'])) {
      $keywords = '';

      $Qattributes = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS  pa.products_related_id,
                                                                     pa.products_related_id_master,
                                                                     pa.products_related_id_slave,
                                                                     pa.products_related_sort_order,
                                                                     pa.products_related,
                                                                     pa.products_cross_sell,
                                                                     pa.products_mode_b2b
                                        from :table_products_related pa left join :table_products_description pd ON pa.products_related_id_master = pd.products_id
                                                                        left join :table_products p on pa.products_related_id_master = p.products_id
                                        where pd.language_id = :language_id
                                        and pa.products_related_id = :products_related_id

                                        group by pd.products_name,
                                                 pa.products_related_sort_order,
                                                 pa.products_related_id
                                        limit :page_set_offset,
                                              :page_set_max_results

                                        ');

      $Qattributes->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qattributes->bindInt(':products_related_id', $_GET['products_related_id']);

      $Qattributes->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qattributes->execute();

    } elseif (isset($_POST['products_id_view']) && !is_null($_POST['products_id_view'])) {

      $Qattributes = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS  pa.products_related_id,
                                                                     pa.products_related_id_master,
                                                                     pa.products_related_id_slave,
                                                                     pa.products_related_sort_order,
                                                                     pa.products_related,
                                                                     pa.products_cross_sell,
                                                                     pa.products_mode_b2b
                                        from :table_products_related pa left join :table_products_description pd ON pa.products_related_id_master = pd.products_id
                                                                        left join :table_products p on pa.products_related_id_master = p.products_id
                                        where pd.language_id = :language_id
                                        and pa.products_related_id_master = :products_related_id_master

                                        group by pd.products_name,
                                                 pa.products_related_sort_order,
                                                 pa.products_related_id
                                        limit :page_set_offset,
                                              :page_set_max_results

                                        ');

      $Qattributes->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qattributes->bindInt(':products_related_id_master', $_POST['products_id_view']);

      $Qattributes->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qattributes->execute();

    } else {

      $Qattributes = $CLICSHOPPING_Db->prepare('select SQL_CALC_FOUND_ROWS  pa.products_related_id,
                                                                     pa.products_related_id_master,
                                                                     pa.products_related_id_slave,
                                                                     pa.products_related_sort_order,
                                                                     pa.products_related,
                                                                     pa.products_cross_sell,
                                                                     pa.products_mode_b2b
                                        from :table_products_related pa left join :table_products_description pd ON pa.products_related_id_master = pd.products_id
                                                                        left join :table_products p on pa.products_related_id_master = p.products_id
                                        where pd.language_id = :language_id
                                        group by pd.products_name,
                                                 pa.products_related_sort_order,
                                                 pa.products_related_id
                                        limit :page_set_offset,
                                              :page_set_max_results

                                        ');

      $Qattributes->bindInt(':language_id', $CLICSHOPPING_Language->getId());
      $Qattributes->setPageSet((int)MAX_DISPLAY_SEARCH_RESULTS_ADMIN);
      $Qattributes->execute();
    }


    echo HTML::form('products_related', $CLICSHOPPING_Related->link('ProductsRelated' . $form_params), 'post', 'class="form-inline"');
  ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <td>
      <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="dataTableHeadingRow">
          <td><?php echo $CLICSHOPPING_Related->getDef('table_heading_id'); ?></td>
          <td><?php echo $CLICSHOPPING_Related->getDef('table_heading_product_from'); ?></td>
          <td><?php echo $CLICSHOPPING_Related->getDef('table_heading_product_to'); ?></td>
          <td
            class="text-center"><?php echo $CLICSHOPPING_Related->getDef('table_heading_cross_sell_products'); ?></td>
          <td class="text-center"><?php echo $CLICSHOPPING_Related->getDef('table_heading_related_products'); ?></td>
          <td class="text-center"><?php echo $CLICSHOPPING_Related->getDef('table_heading_order'); ?></td>
          <?php
            if (MODE_B2B_B2C == 'true') {
              ?>
              <td class="text-center">
                &nbsp;<?php echo $CLICSHOPPING_Related->getDef('table_heading_customers_group'); ?></td>
              <?php
            }
          ?>
          <td class="text-center">&nbsp;<?php echo $CLICSHOPPING_Related->getDef('table_heading_action'); ?></td>
        </tr>
        </thead>
        <tbody>
        <?php

          $next_id = 1;

          while ($Qattributes->fetch()) {

          $products_name_master = $CLICSHOPPING_ProductsAdmin->getProductsName($Qattributes->valueInt('products_related_id_master'));
          $products_name_slave = $CLICSHOPPING_ProductsAdmin->getProductsName($Qattributes->valueInt('products_related_id_slave'));
          $mModel = $CLICSHOPPING_ProductsAdmin->getProductsModel($Qattributes->valueInt('products_related_id_master')) . ' - ';
          $sModel = $CLICSHOPPING_ProductsAdmin->getProductsModel($Qattributes->valueInt('products_related_id_slave')) . ' - ';
          $products_related_sort_order = $Qattributes->valueInt('products_related_sort_order');
          $products_related = $Qattributes->valueInt('products_related');
          $products_cross_sell = $Qattributes->valueInt('products_cross_sell');
          $products_mode_b2b = $Qattributes->valueInt('products_mode_b2b');

          $rows++;
        ?>
        <!-- //################################################################################################################ -->
        <!-- //                                      Update Attributes                                                          -->
        <!-- //################################################################################################################ -->
        <tr class="dataTableRow">
          <?php
            if (isset($_GET['Edit']) && $_GET['products_related_id'] == $Qattributes->valueInt('products_related_id')) {
              ?>
              <th
                scope="row"><?php echo $Qattributes->valueInt('products_related_id'); ?><?php echo HTML::hiddenField('products_related_id', $Qattributes->valueInt('products_related_id')); ?></th>
              <td>&nbsp;<select name="products_related_id_master">
                  <?php
                    $QproductsMaster = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                     p.products_model,
                                                     pd.products_name
                                              from :table_products p,
                                                   :table_products_description pd
                                              where pd.products_id = p.products_id
                                              and pd.language_id = :language_id
                                              and p.products_archive = :products_archive
                                              order by  p.products_model,
                                                        pd.products_name
                                            ');

                    $QproductsMaster->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
                    $QproductsMaster->bindInt(':products_archive', 0);
                    $QproductsMaster->execute();

                    while ($QproductsMaster->fetch()) {
                      $model = $QproductsMaster->value('products_model') . ' - ';
                      $name = $QproductsMaster->value('products_name');
                      $product_name = $name;

                      if ($Qattributes->valueInt('products_related_id_master') == $QproductsMaster->valueInt('products_id')) {
                        echo "\n" . '<option name="' . $QproductsMaster->value('products_name') . '" value="' . $QproductsMaster->valueInt('products_id') . '" SELECTED>' . $model . ' ' . $product_name . '</option>';
                      } else {
                        echo "\n" . '<option name="' . $QproductsMaster->value('products_name') . '" value="' . $QproductsMaster->valueInt('products_id') . '">' . $model . ' ' . $product_name . '</option>';
                      }
                    }
                  ?>
                </select>&nbsp;
              </td>
              <td>&nbsp;<select name="products_related_id_slave">
                  <?php
                    $QproductsSlave = $CLICSHOPPING_Db->prepare('select p.products_id,
                                                   p.products_model,
                                                   pd.products_name
                                            from :table_products p,
                                                 :table_products_description pd
                                            where pd.products_id = p.products_id
                                            and pd.language_id = :language_id
                                            and p.products_archive = :products_archive
                                            order by  p.products_model,
                                                      pd.products_name
                                          ');

                    $QproductsSlave->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
                    $QproductsSlave->bindInt(':products_archive', 0);
                    $QproductsSlave->execute();

                    while ($QproductsSlave->fetch()) {
                      $model = $QproductsSlave->value('products_model') . ' - ';
                      $name = $QproductsSlave->value('products_name');
                      $product_name = $name;

                      if ($Qattributes->valueInt('products_related_id_slave') == $QproductsSlave->valueInt('products_id')) {
                        echo "\n" . '<option name="' . $QproductsSlave->value('products_name') . '" value="' . $QproductsSlave->valueInt('products_id') . '" SELECTED>' . $model . ' ' . $product_name . '</option>';
                      } else {
                        echo "\n" . '<option name="' . $QproductsSlave->value('products_name') . '" value="' . $QproductsSlave->valueint('products_id') . '">' . $model . ' ' . $product_name . '</option>';
                      }

                    }
                  ?>
                </select>&nbsp;
              </td>

              <?php
              if ($products_related == '1') {
                $checkbox = true;
              }

              if ($products_cross_sell == '1') {
                $checkbox1 = true;
              }

              if ($products_mode_b2b == '1') {
                $checkbox2 = true;
              }
              ?>
              <td
                class="dataTableContent text-center"><?php echo HTML::checkboxField('products_cross_sell', $products_cross_sell, $checkbox1); ?></td>
              <td
                class="dataTableContent text-center"><?php echo HTML::checkboxField('products_related', $products_related, $checkbox); ?></td>
              <td
                class="dataTableContent text-center"><?php echo HTML::inputField('products_related_sort_order', $Qattributes->valueInt('products_related_sort_order'), 'size="3"'); ?></td>
              <?php
              if (MODE_B2B_B2C == 'true') {
                ?>
                <td
                  class="text-center"><?php echo HTML::checkboxField('products_mode_b2b', $products_mode_b2b, $checkbox2); ?></td>
                <?php
              }
              ?>
              <td class="text-center" width="190">
                <span>

<?php
  echo HTML::button($CLICSHOPPING_Related->getDef('button_update'), null, null, 'success', null, 'sm') . '&nbsp;';
  echo HTML::button($CLICSHOPPING_Related->getDef('button_cancel'), null, $CLICSHOPPING_Related->link('ProductsRelated&attribute_page=' . $attribute_page . '&products_id_view=' . $products_id_view), 'warning', null, 'sm');
?>
                </span>
              </td>
              <?php
            } else {
              ?>
              <!-- //################################################################################################################ -->
              <!-- //                                     list des liaisons                                                 -->
              <!-- //################################################################################################################ -->

              <td><?php echo $Qattributes->valueInt('products_related_id'); ?></td>
              <td><?php echo $mModel ?><?php echo $products_name_master; ?></td>
              <td><?php echo $sModel ?><?php echo $products_name_slave; ?></td>
              <td>
                <?php
                  if ($products_cross_sell == 1) {
                    echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated&setflagProductsCrossSell&flag_cross=0&products_related_id_master=' . $Qattributes->valueInt('products_related_id_master') . '&products_related_id=' . $Qattributes->valueInt('products_related_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                  } else {
                    echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated&setflagProductsCrossSell&flag_cross=1&products_related_id_master=' . $Qattributes->valueInt('products_related_id_master') . '&products_related_id=' . $Qattributes->valueInt('products_related_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                  }
                ?>
              <td>

                <?php
                  if ($products_related == 1) {
                    echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated&setflagProductsRelated&flag_related=0&products_related_id_master=' . $Qattributes->valueInt('products_related_id_master') . '&products_related_id=' . $Qattributes->valueInt('products_related_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                  } else {
                    echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated&setflagProductsRelated&flag_related=1&products_related_id_master=' . $Qattributes->valueInt('products_related_id_master') . '&products_related_id=' . $Qattributes->valueInt('products_related_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                  }

                ?>
              </td>
              <td><?php echo $products_related_sort_order; ?></td>
              <?php
              if (MODE_B2B_B2C == 'true') {
                ?>
                <td class="text-center">
                  <?php
                    if ($products_mode_b2b != 0) {
                      echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated&setflagProductsB2bStatus&flag_b2b=0&products_related_id_master=' . $Qattributes->valueInt('products_related_id_master') . '&products_related_id=' . $Qattributes->valueInt('products_related_id')) . '"><i class="fas fa-check fa-lg" aria-hidden="true"></i></a>';
                    } else {
                      echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated&setflagProductsB2bStatus&flag_b2b=1&products_related_id_master=' . $Qattributes->valueInt('products_related_id_master') . '&products_related_id=' . $Qattributes->valueInt('products_related_id')) . '"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a>';
                    }
                  ?>
                </td>
                <?php
              }
              ?>
              <td class="text-center">
                <?php
                  $params = '&Edit&products_related_id=' . $Qattributes->valueInt('products_related_id') . '&attribute_page=' . $attribute_page . '&products_id_view=' . $products_id_view;
                  echo '<a href="' . $CLICSHOPPING_Related->link('ProductsRelated' . $params) . '">' . HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/edit.gif', $CLICSHOPPING_Related->getDef('image_edit')) . '</a>&nbsp;';

                  $params = '&ProductsRelated&Remove&delete_attribute&products_related_id=' . $Qattributes->valueInt('products_related_id') . '&attribute_page=' . $attribute_page . '&products_id_view=' . $products_id_view;
                ?>
                <a href="<?php echo $CLICSHOPPING_Related->link('ProductsRelated' . $params); ?>"
                   onclick="return confirm('<?php echo sprintf($CLICSHOPPING_Related->getDef('text_confirm_delete_attribute'), addslashes($products_name_slave), addslashes($products_name_master)); ?>');"><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/delete.gif', $CLICSHOPPING_Related->getDef('image_delete')); ?></a>
              </td>
              <?php
            }

            $QmaxAttributesId = $CLICSHOPPING_Db->prepare('select max(products_related_id) + 1 as next_id
                                               from :table_products_related
                                  ');
            $QmaxAttributesId->execute();

            $next_id = $QmaxAttributesId->valueInt('next_id');
          ?>
        </tr>
        </tbody>
        <!-- //################################################################################################################ -->
        <!-- //                                    Insert data                                                    -->
        <!-- //################################################################################################################ -->
        <?php
          }
          if (!isset($_GET['Edit'])) {
            ?>
            <tr class="<?php echo(floor($rows / 2) == ($rows / 2) ? 'dataTableRowSelected' : 'dataTableRow'); ?>">
              <td>&nbsp;<?php echo $next_id; ?>&nbsp;</td>
              <td><strong>A:</strong>&nbsp;
                <select name="products_related_id_master">
                  <?php
                    $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                           p.products_model,
                                           pd.products_name
                                    from :table_products p,
                                         :table_products_description pd
                                    where pd.products_id = p.products_id
                                    and pd.language_id = :language_id
                                    and p.products_archive = :products_archive
                                    order by  p.products_model,
                                              pd.products_name
                                   ');

                    $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
                    $Qproducts->bindInt(':products_archive', 0);
                    $Qproducts->execute();

                    $products_related_id_master = $_GET['products_related_id_master'];

                    if (!$products_related_id_master) {
                      $products_related_id_master = $products_id_view;
                    }

                    while ($products_values = $Qproducts->fetch()) {
                      $model = $products_values['products_model'] . ' - ';
                      $name = $products_values['products_name'];
                      $product_name = $name;

                      if ($products_related_id_master == $products_values['products_id']) {
                        echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '" SELECTED>' . $model . $product_name . '</option>';
                      } else {
                        echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '">' . $model . $product_name . '</option>';
                      }
                    }
                  ?>
                </select>&nbsp;
              </td>
              <td><strong>B:</strong>&nbsp;
                <select name="products_related_id_slave">
                  <?php
                    $Qproducts = $CLICSHOPPING_Db->prepare('select p.products_id,
                                             p.products_model,
                                             pd.products_name
                                      from :table_products p,
                                           :table_products_description pd
                                      where pd.products_id = p.products_id
                                      and pd.language_id = :language_id
                                      and p.products_archive = :products_archive
                                      order by  p.products_model,
                                                pd.products_name
                                     ');

                    $Qproducts->bindInt(':language_id', (int)$CLICSHOPPING_Language->getId());
                    $Qproducts->bindInt(':products_archive', 0);
                    $Qproducts->execute();

                    while ($products_values = $Qproducts->fetch()) {
                      $model = $products_values['products_model'] . ' - ';
                      $name = $products_values['products_name'];
                      $product_name = $name;

                      if ($_GET['products_related_id_slave'] == $products_values['products_id']) {
                        echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '" SELECTED>' . $model . $product_name . '</option>';
                      } else {
                        echo '<option name="' . $products_values['products_name'] . '" value="' . $products_values['products_id'] . '">' . $model . $product_name . '</option>';
                      }
                    }
                  ?>
                </select>&nbsp;
              </td>

              <td
                class="dataTableContent text-center"><?php echo HTML::checkboxField('products_cross_sell', '1', false); ?></td>
              <td
                class="dataTableContent text-center"><?php echo HTML::checkboxField('products_related', '1', false); ?></td>
              <td
                class="dataTableContent text-center"><?php echo HTML::inputField('products_related_sort_order', '', 'size="3"'); ?></td>
              <?php
                if (MODE_B2B_B2C == 'true') {
                  ?>
                  <td
                    class="dataTableContent text-center"><?php echo HTML::checkboxField('products_mode_b2b', '1', false); ?></td>
                  <?php
                }
              ?>
              <td class="text-end">
                <?php echo HTML::button($CLICSHOPPING_Related->getDef('button_insert'), null, null, 'success', ['params' => 'formaction="' . $CLICSHOPPING_Related->link('ProductsRelated&Insert') . '"'], 'sm'); ?>
                <?php echo HTML::button($CLICSHOPPING_Related->getDef('button_reciprocate'), null, null, 'primary', ['params' => 'formaction="' . $CLICSHOPPING_Related->link('ProductsRelated&Reciprocate') . '"'], 'sm'); ?>
              </td>
            </tr>
            <?php
          }
          echo HTML::hiddenField('products_id_view', $products_id_view);
        ?>
      </table>
    </td>
  </table>
</div>
</form>

<div class="separator"></div>
<div class="alert alert-info" role="alert">
  <div><?php echo HTML::image($CLICSHOPPING_Template->getImageDirectory() . 'icons/help.gif', $CLICSHOPPING_Related->getDef('title_help_related_products')) . ' ' . $CLICSHOPPING_Related->getDef('title_help_related_products') ?></div>
  <div class="separator"></div>
  <div><?php echo $CLICSHOPPING_Related->getDef('text_help_related_products_content'); ?></div>
</div>

