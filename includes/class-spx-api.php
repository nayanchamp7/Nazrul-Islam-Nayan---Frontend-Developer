<?php

/**
 * SPX API
 *
 * @category Class
 * @package  SPX_API
 * @author   Nazrul Islam Nayan <nazrulislamnayan7@gmail.com>
 * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link     https://github.com/nayanchamp7/Nazrul-Islam-Nayan---Frontend-Developer.git
 */

defined('ABSPATH') || exit;

if ( ! class_exists('SPX_API') ) {
    /**
     * SpaceX API class
     *
     * @category Class
     * @package  SPX_API
     * @author   Nazrul Islam Nayan <nazrulislamnayan7@gmail.com>
     * @license  https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
     * @link     https://github.com/nayanchamp7/Nazrul-Islam-Nayan---Frontend-Developer.git
     */
    class SPX_API
    {

        protected static $instance = null;

        /**
         * Constructor
         */
        function __construct()
        {

            $this->initRestApi();
            do_action('spx_api_loaded', $this);
        }

        /**
         * Instance
         *
         * @return SPX_API|null
         */
        public static function instance()
        {
            if ( is_null(self::$instance) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Init rest api
         *
         * @return void
         */
        function initRestApi()
        {
            add_action('rest_api_init',  [$this, 'registerApi']);
        }

        /**
         * Create api endpoints
         *
         * @return void|false
         */
        function registerApi()
        {

            // when user is not logged in, don't access endpoint
            if( ! is_user_logged_in() ) {
                return false;
            }

            // register endpoint to get capsules
            register_rest_route(
                'spx/v1',
                '/capsules/',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'getCapsules'],
                    'permission_callback' => '__return_true',
                )
            );

            // register endpoint to get launches
            register_rest_route(
                'spx/v1',
                '/launches/',
                array(
                    'methods' => 'GET',
                    'callback' => [$this, 'getLaunches'],
                    'permission_callback' => '__return_true',
                )
            );
        }

        /**
         * Get spacex capsules via api
         *
         * @param $request object api request object
         *
         * @return WP_REST_Response
         * @throws Exception
         */
        function getCapsules($request)
        {

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

                if(isset($filter_by) && $filter_value ) {
                    if (in_array($filter_by, $available_filters) ) {
                        $body_args['query'][$filter_by] = $filter_value;
                    }
                }
            }

            //option arguments for the query
            $option_args = [
                "limit" => $per_page,
                "page" => $page,
            ];
            $body_args['options'] = $option_args;

            // body arguments
            $args = array(
                'body' => $body_args,
            );

            // get the api response
            $response = wp_remote_post($api_url, $args);

            // set api body response and messages
            if ( ! is_wp_error($response) ) {
                $responseBody = json_decode(wp_remote_retrieve_body($response), true);
            } else {
                $error_message = $response->get_error_message();
                throw new Exception($error_message);
            }

            return new WP_REST_Response(
                [
                'data' => $responseBody,
                'count' => isset($responseBody["docs"]) ? count($responseBody["docs"]) : 0,
                ]
            );
        }

        /**
         * Get spacex launces via api
         *
         * @param $request object api request object
         *
         * @return WP_REST_Response
         * @throws Exception
         */
        function getLaunches($request)
        {

            // per page parameter
            $id = $request->get_param('id');
            $launch_id = isset($id) ? $id : "";

            if( empty($launch_id) ) {
                $responseBody = __("Launch id parameter missing", "spacex-craft");
            }else {
                // api url
                $api_url = 'https://api.spacexdata.com/v4/launches/' . $launch_id;

                // get the api response
                $response = wp_remote_get($api_url);

                // set api body response and messages
                if (! is_wp_error($response) ) {
                    $responseBody = json_decode(wp_remote_retrieve_body($response), true);
                } else {
                    $error_message = $response->get_error_message();
                    throw new Exception($error_message);
                }
            }

            return new WP_REST_Response(
                [
                    'data' => $responseBody,
                ]
            );
        }

    }
}
