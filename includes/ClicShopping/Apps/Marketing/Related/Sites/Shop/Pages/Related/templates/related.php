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

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  $CLICSHOPPING_Page = Registry::get('Site')->getPage();

  require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('header'));

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  $position = 1;
?>
  <div class="contentText">
    <div class="col-md-12">
      <div class="page-header AppRelatedPageHeader">
        <h1><?php echo $CLICSHOPPING_Related->getDef('text_heading_title_best_selling'); ?></h1>
      </div>
    </div>

    <div class="d-flex flex-wrap ">
      <?php
        foreach ($related as $b) {
          $products_id = $b['products_id'];
          ?>
          <div class="col-md-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
            <div style="padding-top:1rem;"></div>
            <div class="card-deck-wrapper" itemprop="itemListElement" itemscope=""
                 itemtype="https://schema.org/Product">
              <div class="card-deck">
                <div class="card">
                  <div class="card-header AppRelatedCardHeader">
                    <div class="col-md-12">
                      <span class="col-md-1"><?php echo $position; ?></span>
                      <span
                        class="col-md-11"><?php echo HTML::link(CLICSHOPPING::link(null, 'Marketing&Description&products_id=' . $products_id), '<span itemprop="itemListElement">' . $b['products_name'] . '</span></a><br />'); ?></span>
                    </div>
                  </div>
                  <div class="card-img-top text-center AppRelatedCardImg">
                    <?php echo HTML::link(CLICSHOPPING::link(null, 'Marketing&Description&products_id=' . $products_id), HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . $CLICSHOPPING_ProductsCommon->getProductsImage(), $b['products_name'], (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, null, true)); ?>
                  </div>
                  <div class="card-text">
                    <div class="text-center AppRelatedPrice">
                      <?php echo $CLICSHOPPING_ProductsCommon->getCustomersPrice(); ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          $position++;
        }
      ?>
    </div>
  </div>
<?php
  require_once($CLICSHOPPING_Template->getTemplateHeaderFooter('footer'));