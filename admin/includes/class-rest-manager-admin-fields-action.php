<?php

/**
 * The admin-specific includes functionality of the plugin.
 *
 * @link       http://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/admin/includes
 */


/**
 * The admin-specific includes functionality of the plugin.
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/admin/includes
 * @author     RedCastor <team@redcastor.io>
 */
class Rest_Manager_Admin_Fields_Action {

  /**
   * Rest Api decode param
   *
   * @param $new_value
   * @param $old_value
   * @return mixed
   */
  static public function routes_decode ( $new_routes, $old_routes) {

    if ( !is_array($new_routes) ) {
      return $new_routes;
    }

    $new_routes = array_combine(array_map('urldecode', array_keys($new_routes)), $new_routes);

    return $new_routes;
  }

}
