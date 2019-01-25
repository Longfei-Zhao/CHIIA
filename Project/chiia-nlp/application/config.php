<?php

return [
    // +----------------------------------------------------------------------
    // | application settings
    // +----------------------------------------------------------------------

    // debug model
    'app_debug'              => false,
    // application trace
    'app_trace'              => false,
    // application status
    'app_status'             => '',
    // application multiple module support
    'app_multi_module'       => true,
    // automatically bind module
    'auto_bind_module'       => false,
    // root namespace
    'root_namespace'         => [],
    // extra function file
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // default return type
    'default_return_type'    => 'html',
    // default data return type, default AJAX, the value can be json,xml etc.
    'default_ajax_return'    => 'json',
    // default handler, JSONP type return
    'default_jsonp_handler'  => 'jsonpReturn',
    // default JSONP handler
    'var_jsonp_handler'      => 'callback',
    // default timezone
    'default_timezone'       => 'Australia/Sydney',
    // multiple language switch
    'lang_switch_on'         => false,
    // default filter
    'default_filter'         => '',
    // default language
    'default_lang'           => 'zh-cn',
    // suffix of class
    'class_suffix'           => false,
    // suffix of controller
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | module setting
    // +----------------------------------------------------------------------

    // default module
    'default_module'         => 'chiia',
    // deny module list
    'deny_module_list'       => ['common'],
    // default controller
    'default_controller'     => 'Index',
    // default action
    'default_action'         => 'login',
    // default validate
    'default_validate'       => '',
    // default empty controller
    'empty_controller'       => 'Error',
    // action function suffix
    'action_suffix'          => '',
    // automatically search controller
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL settings
    // +----------------------------------------------------------------------

    // variable of PATHINFO, used for compatibility module
    'var_pathinfo'           => 's',
    // compatibility PATH_INFO fetch
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo departure
    'pathinfo_depr'          => '/',
    // URL html suffix
    'url_html_suffix'        => 'html',
    // URL common parameter
    'url_common_param'       => false,
    // URL parameter parsing type, 0 parsing by name, 1 parsing by order
    'url_param_type'         => 0,
    // url route switch
    'url_route_on'           => true,
    // route complete match switch
    'route_complete_match'   => false,
    // route config file, support multi
    'route_config_file'      => ['route'],
    // compulsory url route switch
    'url_route_must'         => false,
    // url domain deploy
    'url_domain_deploy'      => false,
    // url domain root
    'url_domain_root'        => '',
    // url convert for convert controller and action automatically
    'url_convert'            => true,
    // default url controller layer
    'url_controller_layer'   => 'controller',
    // var method
    'var_method'             => '_method',
    // var ajax
    'var_ajax'               => '_ajax',
    // var pjax
    'var_pjax'               => '_pjax',
    // cache of request switch
    'request_cache'          => false,
    // cache of request validity time
    'request_cache_expire'   => null,
    // cache of request except rule
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | template setting
    // +----------------------------------------------------------------------

    'template'               => [
        'type'         => 'Think',
        // template path
        'view_path'    => '',
        // template suffix
        'view_suffix'  => 'html',
        // template departure
        'view_depr'    => DS,
        // template begin tag
        'tpl_begin'    => '{',
        // template end tag
        'tpl_end'      => '}',
        // tag lib begin tag
        'taglib_begin' => '{',
        // tag lib end tag
        'taglib_end'   => '}',
    ],

    // view replace string
    'view_replace_str'       => [],
    // default dispatch template
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | exception & error settings
    // +----------------------------------------------------------------------

    // exception module template
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // error message for non-debug mode
    'error_message'          => 'ERROR PAGE ',
    // error message switch
    'show_error_msg'         => false,
    // exception handler \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | log setting
    // +----------------------------------------------------------------------

    'log'                    => [
        'type'  => 'File',
        // log path
        'path'  => LOG_PATH,
        // log level
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace setting
    // +----------------------------------------------------------------------
    'trace'                  => [
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | cache setting
    // +----------------------------------------------------------------------

    'cache'                  => [
        'type'   => 'File',
        // cache path
        'path'   => CACHE_PATH,
        // cache prefix
        'prefix' => '',
        // cache period, 0 for permanent
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | session settings
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID var
        'var_session_id' => '',
        // SESSION prefix
        'prefix'         => 'think',
        // SESSION type, supporting redis memcache memcached
        'type'           => '',
        // switch: automatically start SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie setting
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie prefix
        'prefix'    => '',
        // cookie time
        'expire'    => 0,
        // cookie path
        'path'      => '/',
        // cookie domain
        'domain'    => '',
        //  cookie security transportation
        'secure'    => false,
        // httponly settings
        'httponly'  => '',
        // setcookie switch
        'setcookie' => true,
    ],

    //page settings
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
];
