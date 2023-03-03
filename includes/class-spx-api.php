<?php

/**
 * SPX API
 *
 * @package    SPX_API
 */

defined('ABSPATH') || exit;

if ( ! class_exists('SPX_API') ) {
    class SPX_API {

        protected static $_instance = null;

        function __construct() {

            $this->init_rest_api();
            do_action( 'spx_api_loaded', $this );
        }

        // instance
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        // init rest api
        function init_rest_api() {
            add_action('rest_api_init',  [$this, 'spx_register_api']);
        }

        // create api endpoints
        function spx_register_api() {

            // register endpoint to get icon list
            register_rest_route(
                'spx/v1',
                '/capsules/',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'spx_get_capsules'],
                    'permission_callback' => '__return_true',
                )
            );
        }

        // get spacex capsules via api
        function spx_get_capsules($request) {

            // api url
            $api_url = 'https://api.spacexdata.com/v4/capsules/query';

            // per page parameter
            $per_page = $request->get_param('per_page');
            $per_page = isset($per_page) ? $per_page : 4;

            // page parameter
            $page = $request->get_param('page');
            $page = isset($page) ? $page : 1;

            // search parameter
            $search     = $request->get_param('search');
            $is_search  = isset($search) && $search == "yes" ? true : false;

            // init body arguments
            $body_args = [];
            $body_args['query'] = [];

            // when search parameter exist, go with filter and search query
            if( $is_search ) {
                // filter parameter for filtering the query
                $filter_by = $request->get_param('filter');
                $filter_value = $request->get_param('filter-value');
                $available_filters = [
                    "serial",
                    "status",
                    "type",
                ];

                if( isset($filter_by) && $filter_value && in_array($filter_by, $available_filters) ) {
                    $body_args['query'][$filter_by] = $filter_value;
                }
            }

            //offset for pagination
            $target_offset = $per_page * $page;
            $target_offset = !empty($target_offset) && $page > 1 ? $target_offset : 0;

            //option arguments for the query
            $option_args = [
                "offset" => $target_offset,
                "limit" => $per_page,
            ];
            $body_args['options'] = $option_args;

            // body arguments
            $args = array(
                'body' => $body_args,
            );

            // get the api response
            $response = wp_remote_post( $api_url, $args );

            // set api body response and messages
            if ( ! is_wp_error( $response ) ) {
                $responseBody = json_decode( wp_remote_retrieve_body( $response ), true );
            } else {
                $error_message = $response->get_error_message();
                throw new Exception( $error_message );
            }

            return new WP_REST_Response([
                'data' => $responseBody,
                'count' => isset($responseBody["docs"]) ? count($responseBody["docs"]) : 0,
            ]);
        }

    }
}
