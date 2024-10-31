<?php
/*
Plugin Name: payzito
Description: payzito
Version: 1.1.2
Author: payzito
*/

use payzitoRepository\libraries\helper;
use payzitoRepository\libraries\controller;

if(is_admin())
{
    $page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';

    defined('DS') OR define('DS',DIRECTORY_SEPARATOR);
    defined('PAR_EXEC') OR define('PAR_EXEC',1);
    defined('PAYZITO_REPOSITORY_ROOT') OR define('PAYZITO_REPOSITORY_ROOT',dirname(__FILE__).DS.'repository');

    include_once PAYZITO_REPOSITORY_ROOT.DS.'libraries'.DS.'helper.php';
    include_once PAYZITO_REPOSITORY_ROOT.DS.'libraries'.DS.'controller.php';
    include_once PAYZITO_REPOSITORY_ROOT.DS.'libraries'.DS.'tag.php';

    function payzito_repository_menu()
    {
        helper::loadLanguages();

        $mainSlug = 'payzito-panel';

        add_menu_page('payzito-repository', helper::_('PA_BACKEND_MENU_MAIN'), 'activate_plugins', $mainSlug, 'payzito_repository_html', plugins_url('repository/assets/images/', __FILE__) . 'menus/payzito.png',4);

        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_PANEL'),'manage_options',$mainSlug);
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_TRANSACTIONS'),'manage_options','payzito-transactions','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_PLUGINS'),'manage_options','payzito-plugins','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_FORMS'),'manage_options','payzito-forms','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_REPORTS'),'manage_options','payzito-reports','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_MEMBERS'),'manage_options','payzito-members','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_OFFERS'),'manage_options','payzito-offers','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_CONFIGURATION'),'manage_options','payzito-configuration','payzito_repository_html');
        add_submenu_page($mainSlug,helper::_('PA_BACKEND_MENU_MAIN'),helper::_('PA_BACKEND_MENU_MESSAGES'),'manage_options','payzito-messages','payzito_repository_html');
    }

    add_action('admin_menu', 'payzito_repository_menu');

    add_action('admin_init',function(){
        $page =  isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';

        if(strpos($page,'payzito') !== false)
        {
            $subpage = str_replace('payzito-','',$page);

            if(!in_array($subpage,['panel','transactions','plugins','reports','members','users','offers','forms','configuration']))
            {
                wp_redirect('admin.php?page=payzito-panel&repository=yes');
            }
            if(!isset($_REQUEST['repository']))
            {
                $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                wp_redirect($url.(strpos($url,'?') !== false ? '&' : '?').'repository=yes');
            }

            add_filter('admin_title', function($title){
                return str_replace('پِی زیتو','پِی زیتو repository',$title);
            });
        }
    });



    function payzito_repository_html()
    {
        $page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : '';
        $subpage = str_replace('payzito-','',$page);

        $controller = new controller();
        $controller->load(!empty($subpage) ? $subpage : 'panel');
    }

    add_action('wp_ajax_payzito',function (){
        $method = isset($_REQUEST['pa-method']) ? sanitize_text_field($_REQUEST['pa-method']) : null;
        $controller = new controller();
        $controller->load('panel',$method);
        die();
    });

    if(strpos($page,'payzito') !== false)
    {
        add_action('admin_enqueue_scripts',function() use ($page){
            wp_enqueue_script( 'payzito-js-chart','https://www.gstatic.com/charts/loader.js');
            wp_enqueue_script( 'payzito-js-jq',plugin_dir_url(__FILE__).'repository/assets/scripts/jq/jq.min.js');
            wp_enqueue_script( 'payzito-js-jq-no',plugin_dir_url(__FILE__).'repository/assets/scripts/jq/no.jq.min.js');
            wp_enqueue_script( 'payzito-js-global',plugin_dir_url(__FILE__).'repository/assets/scripts/global/global.min.js');
            wp_enqueue_script( 'payzito-js-backend',plugin_dir_url(__FILE__).'repository/assets/scripts/core/backend.min.js');
            wp_enqueue_script( 'payzito-js-search',plugin_dir_url(__FILE__).'repository/assets/scripts/search/search.min.js');
            wp_enqueue_script( 'payzito-js-installer',plugin_dir_url(__FILE__).'repository/assets/scripts/installer/installer.min.js');
            wp_enqueue_script( 'payzito-js-custom',plugin_dir_url(__FILE__).'repository/assets/scripts/custom/custom.min.js');

            wp_enqueue_style( 'payzito-css-global', plugin_dir_url(__FILE__).'repository/assets/styles/global/global.min.css');
            wp_enqueue_style( 'payzito-css-backend', plugin_dir_url(__FILE__).'repository/assets/styles/core/backend.min.css');
            wp_enqueue_style( 'payzito-css-installer', plugin_dir_url(__FILE__).'repository/assets/styles/installer/installer.min.css');
            wp_enqueue_style( 'payzito-css-icons', plugin_dir_url(__FILE__).'repository/assets/styles/icons/backend.min.css');
            wp_enqueue_style( 'payzito-css-dynamic', plugin_dir_url(__FILE__).'repository/assets/styles/dynamic/backend.min.css');
            wp_enqueue_style( 'payzito-css-custom', plugin_dir_url(__FILE__).'repository/assets/styles/custom/custom.min.css');

            if(strpos($page,'panel') !== false)
            {
                wp_enqueue_script( 'payzito-js-panel',plugin_dir_url(__FILE__).'repository/assets/scripts/panel.min.js');
                wp_enqueue_style( 'payzito-css-panel', plugin_dir_url(__FILE__).'repository/assets/styles/panel.min.css');
            }

            if(strpos($page,'transactions') !== false)
            {
                wp_enqueue_script( 'payzito-js-solar-calendar-fa',plugin_dir_url(__FILE__).'repository/assets/scripts/solar-calendar/bootstrap-datepicker.fa.min.js');
                wp_enqueue_script( 'payzito-js-solar-calendar',plugin_dir_url(__FILE__).'repository/assets/scripts/solar-calendar/bootstrap-datepicker.min.js');

                wp_enqueue_style( 'payzito-css-solar-calendar', plugin_dir_url(__FILE__).'repository/assets/styles/solar-calendar/bootstrap-datepicker.min.css');
            }

            if(strpos($page,'configuration') !== false)
            {
                wp_enqueue_style( 'payzito-css-toggle-switch', plugin_dir_url(__FILE__).'repository/assets/styles/toggle-switch/style.min.css');
                wp_enqueue_style( 'payzito-css-media', plugin_dir_url(__FILE__).'repository/assets/styles/cms/media/media.min.css');
            }
        });
    }

    $action = isset($_POST['PAAction']) ? sanitize_text_field($_POST['PAAction']) : null;
    if($action)
    {
        $controller = new controller();
        $controller->load('panel','setup');
        die();
    }

    add_filter('plugin_locale',function($locale,$domain){
        return $domain == 'payzito' ? str_replace('-','_',helper::getLanguageTag()) : $locale;
    },10,2);
}

function payzito_remove_update_notification($value) {
    unset($value->response[plugin_basename(__FILE__)]);
    return $value;
}
add_filter('site_transient_update_plugins', 'payzito_remove_update_notification');

function payzito_auto_update_plugin($update,$item){
    if(in_array($item->slug,['payzito']))
    {
        return false;
    }
    else
    {
        return $update;
    }
}
add_filter('auto_update_plugin','payzito_auto_update_plugin',10,2);

add_filter('upgrader_post_install',function ($response,$extra,$result){
    if(isset($result['destination_name']) && strpos($result['destination_name'],'payzito') !== false)
    {
        $name = $result['destination_name'];

        deactivate_plugins('/'.$name.'/'.$name.'.php');

        if($name == 'payzito')
        {
            $files = [
                dirname(__FILE__).DS.'includes'.DS.'setup'.DS.'ready.payzito',
            ];

            foreach ($files as $file)
            {
                if(file_exists($file))
                {
                    @unlink($file);
                }
            }
        }
    }
},10,3);

function payzito_plugin_activate()
{
    $files = [
        ABSPATH.'wp-content/languages/plugins/payzito-fa_IR.mo',
        ABSPATH.'wp-content/languages/plugins/payzito-fa_IR.po',
        ABSPATH.'wp-content/languages/plugins/payzito-en_GB.mo',
        ABSPATH.'wp-content/languages/plugins/payzito-en_GB.po',
    ];

    foreach ($files as $file)
    {
        if(file_exists($file))
        {
            @unlink($file);
        }
    }
}
register_activation_hook( __FILE__, 'payzito_plugin_activate' );