<?php

/**
 * @file
 * Post update hooks for CivicTheme Default Corporate Content.
 */

use Drupal\block\BlockInterface;
use Drupal\civictheme_content\Helper;
use Drupal\Core\Config\Entity\ConfigEntityUpdater;
use Drupal\Core\Utility\UpdateException;

/**
 * Sets homepage.
 */
function civictheme_content_corporate_post_update_set_homepage() {
  try {
    Helper::setHomepageFromNode('Protecting the environment');
  }
  catch (\Exception $e) {
    throw new UpdateException($e->getMessage());
  }
}

/**
 * Provisions links in footer and updates menu block configurations.
 */
function civictheme_content_corporate_post_update_provision_footer_links() {
  $theme = \Drupal::theme()->getActiveTheme()->getName();

  $map = [
    $theme . '_footer_menu_1' => Helper::findMenuItemByTitle('civictheme-footer', 'Science & research'),
    $theme . '_footer_menu_2' => Helper::findMenuItemByTitle('civictheme-footer', 'News and Events'),
    $theme . '_footer_menu_3' => Helper::findMenuItemByTitle('civictheme-footer', 'Social'),
  ];

  $sandbox = [];
  \Drupal::classResolver(ConfigEntityUpdater::class)
    ->update($sandbox, 'block', function (BlockInterface $block) use ($map) {
      if (strpos($block->getPluginId(), 'menu_block:') === 0) {
        if (!empty($map[$block->id()])) {
          $block_settings = $block->get('settings');
          $block_settings['parent'] = 'civictheme-footer:menu_link_content:' . $map[$block->id()]->uuid();
          $block->set('settings', $block_settings);
          return TRUE;
        }
      }
      return FALSE;
    });
}
