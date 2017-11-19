<?php

/**
 * The public-facing includes functionality Hooks.
 *
 * @link       http://redcastor.io
 * @since      1.0.0
 *
 * @package    Rest-Manager
 * @subpackage Rest-Manager/public/includes
 */







/**
 *
 * @package    Rest-Manager
 * @subpackage Rest-Manager/public/includes
 * @author     RedCastor <team@redcastor.io>
 */
class Rest_Manager_Public_Hooks {

  /**
   * Init Hooks.
   */
  public static function init() {

    //Public Filter HooK
    add_filter('rest_manager_get_options',        array( __CLASS__, 'rest_manager_get_options' ), 10, 2);
    add_filter('rest_manager_get_option',         array( __CLASS__, 'rest_manager_get_option' ), 10, 3);

    add_filter('rest_manager_get_active_routes',  array( __CLASS__, 'rest_manager_get_active_routes' ), 10, 2);
    add_filter('rest_manager_get_route_options',  array( __CLASS__, 'rest_manager_get_route_options' ), 10, 2);
    add_filter('rest_manager_get_route_option',   array( __CLASS__, 'rest_manager_get_route_option' ), 10, 3);

  }



  /**
   * Filter get options
   *
   * @param $empty_value
   * @param $section
   * @return string
   */
  public function rest_manager_get_options ( $empty_value, $section ) {

    return rest_manager_get_option( $section );
  }


  /**
   * Filter get option
   *
   * @param $default
   * @param $option
   * @param $section
   * @return string
   */
  public function rest_manager_get_option ( $default, $option, $section ) {

    return rest_manager_get_option( $option, $section, $default );
  }


  /**
   * Filter get active routes
   *
   * @param array $routes
   * @param bool $on_condition
   * @return array
   */
  public function rest_manager_get_active_routes( $empty_value, $on_condition = true ) {

    return rest_manager_get_active_routes( $on_condition );
  }


  /**
   * Filter get route options
   *
   * @param null $empty_value
   * @param $route
   * @return bool|mixed|null
   */
  public function rest_manager_get_route_options ( $empty_value, $route ) {

    $options = rest_manager_get_route_options( $route );

    if (!$options) {
      return $empty_value;
    }

    return $options;
  }

  /**
   * get route option
   *
   * @param string $default
   * @param $option
   * @param $route
   * @return string
   */
  public function rest_manager_get_route_option ( $default, $option, $route ) {

    return rest_manager_get_route_option( $option, $route, $default );
  }


}
