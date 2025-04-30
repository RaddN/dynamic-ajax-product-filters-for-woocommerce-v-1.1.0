<?php

namespace FilterEverything\Filter;

if ( ! defined('ABSPATH') ) {
    exit;
}

class WpManager
{
    private $requestParser;

    private $filterQueryVars;

    private $isFilterRequest;

    private $em;

    public function init()
    {
        global $wp_rewrite;

        if ( ! defined( 'FLRT_PERMALINKS_ENABLED' ) ) {
            $rewrite = $wp_rewrite->wp_rewrite_rules();
            $permalinksEnabled = ( defined( 'FLRT_FILTERS_PRO' ) && ! empty( $rewrite ) );
            define( 'FLRT_PERMALINKS_ENABLED', $permalinksEnabled );
        }

        $this->requestParser = new RequestParser( $this->prepareRequest() );
        $this->em = Container::instance()->getEntityManager();
    }

    public function parseRequest($WP)
    {
        if ( $this->requestParser->detectFilterRequest() ) {
            foreach ( $this->requestParser->getQueryVars() as $key => $queryVar ) {
                $this->setQueryVar( $key, $queryVar );
            }

            $this->isFilterRequest = true;
            $this->setQueryVar('wpc_is_filter_request', true );

            if ( $this->getQueryVar('error') === '404' ) {
                $WP->set_query_var( 'error', '404' );
                return false;
            }
        }
    }
    public function addFilterQueryToWpQuery( $wp_query )
    {
        global $wpc_not_fired;

        if ( $wp_query->is_main_query() && $wpc_not_fired ) {
            global $flrt_sets;

            $filterSet = Container::instance()->getFilterSetService();

            $this->setQueryVar('wp_queried_object', $this->identifyWpQueriedObject($wp_query) );
            $sets = $filterSet->findRelevantSets( $this->getQueryVar('wp_queried_object') );


            $flrt_sets = $sets;
            $this->setQueryVar('wpc_page_related_set_ids', $sets);

            do_action( 'wpc_related_set_ids', $sets );

            if( $this->isFilterRequest() ){

                if (!$filterSet->validateSets($sets)) {
                    self::make_404($wp_query, 'Invalid Set Ids');
                    return true;
                }
                if (!$this->em->checkForbiddenFilters($this->getQueryVar('queried_values'), $this->em->getOnlyBelongsFilters($sets))) {
                    self::make_404($wp_query, 'Forbidden filter requested');
                    return true;
                }
            }
            
            $wpc_not_fired = false;
        }

        
        $setIds = $this->isFilteredQuery( $wp_query );

        
        if ( ! empty( $setIds ) ){

            
            $to_save = clone $wp_query;
            foreach ( $setIds as $setId ){
                $this->setQueryVar('wpc_set_filter_query_' . $setId, $to_save);
            }
            unset($to_save);
        }

        if ( ! empty( $setIds ) && $this->isFilterRequest() ) {

            $em              = Container::instance()->getEntityManager();
            $set_filter_keys = $em->getSetFilterKeys( $setIds );

            foreach ( $this->getQueryVar('queried_values' ) as $queried_value ) {
                $queried_value_key = $queried_value['entity'].'#'.$queried_value['e_name'];

                if( ! $wp_query->get( 'flrt_query_clone' ) && in_array( $queried_value_key, $set_filter_keys ) ){

                    $do_filter_request = apply_filters( 'wpc_do_filter_request', true, $queried_value, $wp_query );

                    if ( $do_filter_request ) {
                        $wpc_main_query = $this->em->addTermsToWpQuery( $queried_value, $wp_query );
                    } else {
                        $wpc_main_query = $wp_query;
                    }

                    if( method_exists( $wpc_main_query , 'set') ){
                        $wpc_main_query->set( 'flrt_filtered_query', true );
                    }

                    if ( ! ( $wpc_main_query instanceof \WP_Query ) ) {
                        return true;
                    }
                }
            }
        }
        if ( ! empty( $setIds ) && ! $wp_query->get('flrt_query_clone') ) {
            do_action( 'wpc_filtered_query_end', $wp_query );
        }

    }

    private function isFilteredQuery( $query )
    {
        if( defined('FLRT_FILTERS_PRO') && FLRT_FILTERS_PRO ){
            return apply_filters( 'wpc_is_filtered_query', [], $query );
        }

        if( $query->is_main_query() ){
            $sets = $this->getQueryVar('wpc_page_related_set_ids');
            $return = [];

            if( isset( $sets[0]['ID'] ) ){
                $return[] = $sets[0]['ID'];
            }

            return $return;
        }
    }
    public function identifyWpQueriedObject( $wp_query )
    {
        $wp_queried_object = [];

        if (!is_object($wp_query)) {
            return $wp_queried_object;
        }
        if ( $wp_query->is_archive() ) {
            if ($wp_query->is_post_type_archive()) {
                $post_type_object = $wp_query->get_queried_object();
                if (isset($post_type_object->name)) {
                    $wp_queried_object['post_types'][] = $post_type_object->name;
                    if( $post_type_object->name === 'product' ){
                        $wp_queried_object['common'][] = 'shop_page';
                    }

                    if( $wp_query->is_search() ){
                        $wp_queried_object['common'][] = 'search_results';
                    }
                }

            }

            if ($wp_query->is_author()) {
                if( ! isset( $wp_queried_object['post_types'] ) ){
                    $wp_queried_object['post_types'][] = 'post';
                }

                if( $wp_query->get('author_name') ){
                    $wp_queried_object['author'] = $wp_query->get('author_name');
                } else {
                    $user_id    = $wp_query->get('author');
                    $user       = get_user_by('ID', $user_id);
                    if( ! is_null( $user ) && is_object( $user ) && property_exists( $user, 'data' ) && property_exists( $user->data, 'user_nicename' ) ) {
                        $wp_queried_object['author'] = $user->data->user_nicename;
                    }
                }

            }

            if ($wp_query->is_date()) {
                $wp_queried_object['post_types'][] = 'post';
            }
        }if ( $wp_query->is_home() || $wp_query->is_posts_page ) {
            $wp_queried_object['post_types'][] = 'post';
            $wp_queried_object['common'][]     = 'page_for_posts';
        }if ( ! $wp_query->is_singular() && $wp_query->is_front_page() ){
            
            $wp_queried_object['common'][] = 'page_on_front';
        }

        
        if ($wp_query->is_search() && !$wp_query->is_archive()) {
            $wp_queried_object['post_types'][] = ($wp_query->get('post_type')) ? $wp_query->get('post_type') : 'post';
            $wp_queried_object['common'][]     = 'search_results';
        }

        if( $wp_query->is_singular() ){
            
            if( $post_obj = $wp_query->get_queried_object() ) {
                $wp_queried_object['post_types'][]  = isset($post_obj->post_type) ? $post_obj->post_type : false;
                $wp_queried_object['post_id']       = isset($post_obj->ID) ? $post_obj->ID : false;

            }elseif( isset( $wp_query->query['name'] ) && ! isset( $wp_query->query['post_type'] ) && $wp_query->query['name'] ){
                $name   = $wp_query->query['name'];
                $f_post = get_page_by_path( $name, OBJECT, 'post' );

                if ( isset( $f_post->post_type ) ) {
                    $wp_queried_object['post_id']      = $f_post->ID;
                    $wp_queried_object['post_types'][] = $f_post->post_type;
                }
            }elseif( $post_type = $wp_query->get('post_type') ){

                if( is_array( $post_type ) ){
                    $wp_queried_object['post_types'] = $post_type;
                }else{
                    $wp_queried_object['post_types'][] = $post_type;
                }
                $name = ( isset( $wp_query->query['name'] ) ) ? $wp_query->query['name'] : '' ;

                if( $name ) {

                    foreach ( (array) $post_type as $_post_type ) {
                        $ptype_obj = get_post_type_object( $_post_type );
                        if ( ! $ptype_obj ) {
                            continue;
                        }

                        $f_post = get_page_by_path( $name, OBJECT, $_post_type );
                        if ( isset( $f_post->post_type ) ) {
                            $wp_queried_object['post_id'] = $f_post->ID;
                            break;
                        }
                    }

                    unset( $ptype_obj );
                } elseif( $page_id = $wp_query->get('page_id') ){
                    $wp_queried_object['post_id'] = $page_id;
                }

            } elseif( $page_id = $wp_query->get('page_id') ){
                $wp_queried_object['post_types'][] = 'page';
                $wp_queried_object['post_id'] = $page_id;

            } elseif ( $post_id = $wp_query->get('p') ){
                $f_post = get_post( $post_id );
                if( isset( $f_post->post_type ) ){
                    $wp_queried_object['post_types'][]  = $f_post->post_type;
                    $wp_queried_object['post_id']       = $post_id;
                }
            }
            if( isset( $wp_queried_object['post_id'] ) && get_option( 'page_on_front' ) == $wp_queried_object['post_id'] ){
                $wp_queried_object['common'][] = 'page_on_front';
                unset($wp_queried_object['post_id']);
            }

        }
        $postData = Container::instance()->getThePost();
        if( isset( $postData['flrt_ajax_link'] ) && empty( $wp_queried_object ) ){
            $wp_queried_object['post_types'][] = ($wp_query->get('post_type')) ? $wp_query->get('post_type') : 'post';
        }

        return apply_filters( 'wpc_wp_queried_object', $wp_queried_object, $wp_query );
    }

    public function getQueryVar($var, $default = false)
    {
        if (isset($this->filterQueryVars[$var])) {
            return $this->filterQueryVars[$var];
        }
        return $default;
    }

    public function setQueryVar($var, $value)
    {
        if (!isset($this->filterQueryVars[$var])) {
            $this->filterQueryVars[$var] = $value;
            return true;
        }
        return false;
    }

    public static function make_404($wp_query, $message = '')
    {
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        if ($message && FLRT_PLUGIN_DEBUG) {
            echo esc_html( $message );
        }
    }

    public function isFilterRequest()
    {
        return $this->isFilterRequest;
    }

    private function getRequestUri()
    {
        $postData = Container::instance()->getThePost();
        if( isset( $postData['flrt_ajax_link'] ) ){

            $home_url = home_url();

            if( flrt_wpml_active() ){
                $home_url = apply_filters( 'wpml_home_url', home_url() );
            }

            $parts = explode( '?', $home_url );
            $home_url = trim( $parts[0], '/' );

            $res =  str_replace( $home_url, '', $postData['flrt_ajax_link'] );

            if( gettype( $res ) === 'string' ){
                return $res;
            }
        }

        $res = '';

        if( gettype( $_SERVER['REQUEST_URI'] ) === 'string' ){
            $res = $_SERVER['REQUEST_URI'];
        }

        return $res;
    }

    public function customParseRequest( $do_parse_request, $WP, $extra_query_vars ){
        global $wp_rewrite;
        $postData = Container::instance()->getThePost();

        $WP->query_vars       = array();
        $post_type_query_vars = array();

        if ( is_array( $extra_query_vars ) ) {
            $WP->extra_query_vars = & $extra_query_vars;
        } elseif ( ! empty( $extra_query_vars ) ) {
            parse_str( $extra_query_vars, $WP->extra_query_vars );
        }

        $rewrite = $wp_rewrite->wp_rewrite_rules();

        if ( ! empty( $rewrite ) ) {
            $error             = '404';
            $WP->did_permalink = true;

            $pathinfo         = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
            list( $pathinfo ) = explode( '?', $pathinfo );
            $pathinfo         = str_replace( '%', '%25', $pathinfo );
            $request_uri     = $this->getRequestUri();
            $cleanedRequest  = $this->requestParser->cleanUpRequestPathFromFilterSegments( $request_uri );

            list( $req_uri ) = explode( '?', $cleanedRequest );
            $self            = $_SERVER['PHP_SELF'];

            $home_path       = parse_url( home_url(), PHP_URL_PATH );
            $home_path_regex = '';
            if ( is_string( $home_path ) && '' !== $home_path ) {
                $home_path       = trim( $home_path, '/' );
                $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );
            }
            $req_uri  = str_replace( $pathinfo, '', $req_uri );
            $req_uri  = trim( $req_uri, '/' );
            $pathinfo = trim( $pathinfo, '/' );
            $self     = trim( $self, '/' );

            if ( ! empty( $home_path_regex ) ) {
                $req_uri  = preg_replace( $home_path_regex, '', $req_uri );
                $req_uri  = trim( $req_uri, '/' );
                $pathinfo = preg_replace( $home_path_regex, '', $pathinfo );
                $pathinfo = trim( $pathinfo, '/' );
                $self     = preg_replace( $home_path_regex, '', $self );
                $self     = trim( $self, '/' );
            }
            if ( ! empty( $pathinfo ) && ! preg_match( '|^.*' . $wp_rewrite->index . '$|', $pathinfo ) ) {
                $requested_path = $pathinfo;
            } else {                
                if ( $req_uri == $wp_rewrite->index ) {
                    $req_uri = '';
                }
                $requested_path = $req_uri;
            }
            $requested_file = $req_uri;

            $WP->request = $requested_path;
            $this->setQueryVar('wp_request', $requested_path);

            if( $cleanedRequest === strtolower( $request_uri ) ){
                return $do_parse_request;
            }

            $do_parse_request = false;
            $request_match = $requested_path;
            if ( empty( $request_match ) ) {
                if ( isset( $rewrite['$'] ) ) {
                    $WP->matched_rule = '$';
                    $query              = $rewrite['$'];
                    $matches            = array( '' );
                }
            } else {
                foreach ( (array) $rewrite as $match => $query ) {                   
                    if ( ! empty( $requested_file ) && strpos( $match, $requested_file ) === 0 && $requested_file != $requested_path ) {
                        $request_match = $requested_file . '/' . $requested_path;
                    }

                    if ( preg_match( "#^$match#", $request_match, $matches ) ||
                        preg_match( "#^$match#", urldecode( $request_match ), $matches ) ) {

                        if ( $wp_rewrite->use_verbose_page_rules && preg_match( '/pagename=\$matches\[([0-9]+)\]/', $query, $varmatch ) ) {
                            
                            $page = get_page_by_path( $matches[ $varmatch[1] ] );
                            if ( ! $page ) {
                                continue;
                            }

                            $post_status_obj = get_post_status_object( $page->post_status );
                            if ( ! $post_status_obj->public && ! $post_status_obj->protected
                                && ! $post_status_obj->private && $post_status_obj->exclude_from_search ) {
                                continue;
                            }
                        }
                        $WP->matched_rule = $match;
                        break;
                    }
                }
            }

            if ( ! empty( $WP->matched_rule ) ) {
                $query = preg_replace( '!^.+\?!', '', $query );
                $query = addslashes( \WP_MatchesMapRegex::apply( $query, $matches ) );

                $WP->matched_query = $query;

               
                parse_str( $query, $perma_query_vars );

                if ( '404' == $error ) {
                    unset( $error, $_GET['error'] );
                }
            }

             if ( empty( $requested_path ) || $requested_file == $self || strpos( $_SERVER['PHP_SELF'], 'wp-admin/' ) !== false ) {
                unset( $error, $_GET['error'] );

                if (isset($perma_query_vars) && strpos($_SERVER['PHP_SELF'], 'wp-admin/') !== false && (! isset( $postData['flrt_ajax_link'] )) ) {
                    unset($perma_query_vars);
                }

                $WP->did_permalink = false;
            }
        } else {
            return $do_parse_request;
        }
        $WP->public_query_vars = apply_filters( 'query_vars', $WP->public_query_vars );

        foreach ( get_post_types( array(), 'objects' ) as $post_type => $t ) {
            if ( is_post_type_viewable( $t ) && $t->query_var ) {
                $post_type_query_vars[ $t->query_var ] = $post_type;
            }
        }

        foreach ( $WP->public_query_vars as $wpvar ) {
            if ( isset( $WP->extra_query_vars[ $wpvar ] ) ) {
                $WP->query_vars[ $wpvar ] = $WP->extra_query_vars[ $wpvar ];
            } elseif ( isset( $_GET[ $wpvar ] ) && isset( $postData[ $wpvar ] ) && $_GET[ $wpvar ] !== $postData[ $wpvar ] ) {
                wp_die( esc_html__( 'A variable mismatch has been detected.' ), esc_html__( 'Sorry, you are not allowed to view this item.' ), 400 );
            } elseif ( isset( $postData[ $wpvar ] ) ) {
                $WP->query_vars[ $wpvar ] = $postData[ $wpvar ];
            } elseif ( isset( $_GET[ $wpvar ] ) ) {
                $WP->query_vars[ $wpvar ] = $_GET[ $wpvar ];
            } elseif ( isset( $perma_query_vars[ $wpvar ] ) ) {
                $WP->query_vars[ $wpvar ] = $perma_query_vars[ $wpvar ];
            }

            if ( ! empty( $WP->query_vars[ $wpvar ] ) ) {
                if ( ! is_array( $WP->query_vars[ $wpvar ] ) ) {
                    $WP->query_vars[ $wpvar ] = (string) $WP->query_vars[ $wpvar ];
                } else {
                    foreach ( $WP->query_vars[ $wpvar ] as $vkey => $v ) {
                        if ( is_scalar( $v ) ) {
                            $WP->query_vars[ $wpvar ][ $vkey ] = (string) $v;
                        }
                    }
                }

                if ( isset( $post_type_query_vars[ $wpvar ] ) ) {
                    $WP->query_vars['post_type'] = $post_type_query_vars[ $wpvar ];
                    $WP->query_vars['name']      = $WP->query_vars[ $wpvar ];
                }
            }
        }

        foreach ( get_taxonomies( array(), 'objects' ) as $taxonomy => $t ) {
            if ( $t->query_var && isset( $WP->query_vars[ $t->query_var ] ) ) {
                $WP->query_vars[ $t->query_var ] = str_replace( ' ', '+', $WP->query_vars[ $t->query_var ] );
            }
        }

        if ( ! is_admin() ) {
            foreach ( get_taxonomies( array( 'publicly_queryable' => false ), 'objects' ) as $taxonomy => $t ) {
                if ( isset( $WP->query_vars['taxonomy'] ) && $taxonomy === $WP->query_vars['taxonomy'] ) {
                    unset( $WP->query_vars['taxonomy'], $WP->query_vars['term'] );
                }
            }
        }

        if ( isset( $WP->query_vars['post_type'] ) ) {
            $queryable_post_types = get_post_types( array( 'publicly_queryable' => true ) );
            if ( ! is_array( $WP->query_vars['post_type'] ) ) {
                if ( ! in_array( $WP->query_vars['post_type'], $queryable_post_types, true ) ) {
                    unset( $WP->query_vars['post_type'] );
                }
            } else {
                $WP->query_vars['post_type'] = array_intersect( $WP->query_vars['post_type'], $queryable_post_types );
            }
        }

       $WP->query_vars = wp_resolve_numeric_slug_conflicts( $WP->query_vars );

        foreach ( (array) $WP->private_query_vars as $var ) {
            if ( isset( $WP->extra_query_vars[ $var ] ) ) {
                $WP->query_vars[ $var ] = $WP->extra_query_vars[ $var ];
            }
        }

        if ( isset( $error ) ) {
            $WP->query_vars['error'] = $error;
        }

        $WP->query_vars = apply_filters( 'request', $WP->query_vars );
        do_action_ref_array( 'parse_request', array( &$WP ) );

        $WP->query_posts();
        $WP->handle_404();
        $WP->register_globals();

        return $do_parse_request;
    }

    private function prepareRequest(){
        global $wp_rewrite;

        $pathinfo         = isset( $_SERVER['PATH_INFO'] ) ? $_SERVER['PATH_INFO'] : '';
        list( $pathinfo ) = explode( '?', $pathinfo );
        $pathinfo         = str_replace( '%', '%25', $pathinfo );

        list( $req_uri ) = explode( '?', $this->getRequestUri() );

        $home_path       = parse_url( home_url(), PHP_URL_PATH );
        $home_path_regex = '';

        if ( is_string( $home_path ) && '' !== $home_path ) {
            $home_path       = trim( $home_path, '/' );
            $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );
        }
        $req_uri  = str_replace( $pathinfo, '', $req_uri );
        $req_uri  = trim( $req_uri, '/' );
        $pathinfo = trim( $pathinfo, '/' );

        if ( ! empty( $home_path_regex ) ) {
            $req_uri  = preg_replace( $home_path_regex, '', $req_uri );
            $req_uri  = trim( $req_uri, '/' );
            $pathinfo = preg_replace( $home_path_regex, '', $pathinfo );
            $pathinfo = trim( $pathinfo, '/' );
        }
        if ( ! empty( $pathinfo ) && ! preg_match( '|^.*' . $wp_rewrite->index . '$|', $pathinfo ) ) {
            $requested_path = $pathinfo;
        } else {
           if ( $req_uri == $wp_rewrite->index ) {
                $req_uri = '';
            }
            $requested_path = $req_uri;
        }

        return $requested_path;
    }

}