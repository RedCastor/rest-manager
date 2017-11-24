<?php

/**
 * The file that defines the settings fields option descriptor
 *
 * @link       team@redcastor.io
 * @since      1.0.0
 *
 * @package    Rest_Manager
 * @subpackage Rest_Manager/includes
 */


/**
 * Define general settings fields
 */
function rest_manager_settings_fields()
{

  $fields = array(
    'rest_manager_routes' => array(
      'title' => __('Rest API', 'rest-manager'),
      'sections' => array(
        'routes' => array(
          'display'=> 'table',
          'fields' => array(),
          'action' => array( 'Rest_Manager_Admin_Fields_Action', 'routes_decode' ),
        ),
      ),
    ),
  );

  return apply_filters('rest_manager_settings_fields', $fields);
}


/**
 * Define general settings routes fields
 */
function rest_manager_settings_route_fields()
{

  $route_fields = array();

  $rest_server = rest_get_server();
  $rest_routes = $rest_server->get_routes();

  $exclude_routes = (array) $rest_server->get_namespaces();
  $exclude_routes[] = '';
  $exclude_routes = apply_filters( 'rest_manager_exclude_routes', $exclude_routes);



  foreach ((array) $rest_routes as $route => $endpoint) {

    if (in_array(ltrim($route, '/'), $exclude_routes)) {
      continue;
    }


    $route_fields[] = array(
      'name'  => $route,
      'encode' => 'urlencode',
      'label' => esc_html( $route ),
      'desc'  => '',
      'type'        => 'sub_fields',
      'sub_fields' => array(
        array(
          'name'        => 'active',
          'label'       => 'Active',
          'default'     => 'on',
          'type'        => 'checkbox',
        ),
        array(
          'name'        => 'plugins',
          'label'       => 'Plugins',
          'desc'        => __( 'Filter plugins', 'rest-manager'),
          'default'     => 'off',
          'type'        => 'checkbox',
        ),
      ),
    );

  }

  return apply_filters('rest_manager_settings_route_fields', $route_fields);
}











