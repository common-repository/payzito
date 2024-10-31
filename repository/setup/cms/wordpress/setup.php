<?php

defined('PAR_EXEC') OR die('Payzito Repository Restricted Access');

class PASetupWordpress implements PASetupImplements
{
    static function addScript($dir)
    {
        if(strpos($dir,'.min.js') !== false)
        {
            $file = ABSPATH.'wp-content'.DS.'plugins'.DS.'payzito'.DS.'includes'.DS.'assets'.DS.'scripts'.DS.str_replace('/',DS,substr($dir,0,strpos($dir,'?')));
            if(!file_exists($file))
            {
                $dir = str_replace('.min.js','.js',$dir);
            }
        }

        $url = self::getRootUri().'wp-content/plugins/payzito/includes/assets/scripts/'.$dir;

        wp_enqueue_script(self::getAssetsHash($url),$url,['jquery']);
    }

    static function addScriptDeclaration($content)
    {
        $action = (self::isBackend() ? 'admin' : 'wp') . '_head';
        add_action($action,function() use ($content){
            echo '<script type="text/javascript">'.$content.'</script>';
        });
    }

    static function addStyle($dir)
    {
        if(strpos($dir,'.min.css') !== false)
        {
            $file = ABSPATH.'wp-content'.DS.'plugins'.DS.'payzito'.DS.'includes'.DS.'assets'.DS.'styles'.DS.str_replace('/',DS,substr($dir,0,strpos($dir,'?')));
            if(!file_exists($file))
            {
                $dir = str_replace('.min.css','.css',$dir);
            }
        }

        $url = self::getRootUri().'wp-content/plugins/payzito/includes/assets/styles/'.$dir;

        wp_enqueue_style(self::getAssetsHash($url),$url);
    }

    static function getPluginPath($data)
    {
        return self::getRootPath().DS.'wp-content'.DS.'plugins'.DS.$data['plg_name'];
    }

    static function getAdminUri($pathOnly=false)
    {
        $siteUrl = get_admin_url();

        if($pathOnly)
        {
            $domain = self::getDomain();
            $siteUrl = substr($siteUrl,0,strlen($domain)) == $domain ? substr($siteUrl,strlen($domain)) : $siteUrl;
        }

        return rtrim($siteUrl,'/').'/';
    }

    static function getBasePath()
    {
        return self::isBackend() ? self::getAdminPath() : self::getRootPath();
    }

    static function getRootPath()
    {
        return rtrim(str_replace('/',DS,ABSPATH),DS);
    }

    static function getCorePath()
    {
        return self::getRootPath().DS.'wp-content'.DS.'plugins'.DS.'payzito'.DS.'includes';
    }

    static function getRootUri($pathOnly=false)
    {
        $siteUrl = get_site_url();

        if($pathOnly)
        {
            $siteUrl = wp_make_link_relative($siteUrl);
        }

        return rtrim($siteUrl,'/').'/';
    }

    static function getBackendUrl($src)
    {
        return 'admin.php?page=payzito-'.$src;
    }

    static function setSession($name,$value)
    {
        self::startSession();
        $_SESSION[$name] = $value;
    }

    static function getSession($name)
    {
        self::startSession();
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    static function clearSession($name)
    {
        self::startSession();
        unset($_SESSION[$name]);
    }

    static function getSampleAction()
    {
        return [
            'data' => [
                [
                    'PAAction' => 'download',
                    'PAElement' => '{NAME}',
                    'PAFileName' => '{PLUGIN_NAME}.zip',
                ],
                [
                    'PAAction' => 'install',
                    'PAFileName' => '{PLUGIN_NAME}.zip',
                    'PAIsAddon' => 1,
                ],
            ],
            'label' => '{LABEL}',
            'skipIfFail' => true,
        ];
    }

    static function inInstallArea()
    {
        return true;
    }

    static function enableInstallerPlugin()
    {
        return;
    }

    static function getMediaPath()
    {
        return self::getCorePath().DS.'assets';
    }

    static function moveMediaFiles()
    {
        return false;
    }

    static function moveCoreFiles()
    {
        return false;
    }

    static function movePackageFiles()
    {
        return false;
    }

    static function dbQuery($query)
    {
        global $wpdb;
        return $wpdb->query(self::modifyQuery($query));
    }

    static function getDomain()
    {
        return parse_url(get_home_url(),PHP_URL_HOST);
    }

    static function tableExist($table)
    {
        $allTables = self::getAllTables();
        $table = str_replace('#__',self::getTablePrefix(),$table);
        return in_array($table,$allTables) ? true : false;
    }

    static function existAtLeastRow($table,$where='')
    {
        return self::getCountOfTable($table,$where) > 0 ? true : false;
    }

    static function getCountOfTable($table,$where='')
    {
        $query = "SELECT COUNT(*) AS `count` FROM `".$table."` ".(!empty($where) ? 'WHERE '.$where : '');

        global $wpdb;
        $query = self::modifyQuery($query);
        $result = $wpdb->get_row($query,ARRAY_A);
        return !empty($result['count']) ? $result['count'] : 0;
    }

    static function isLocalHost()
    {
        $host = self::getDomain();
        return strpos($host,'localhost') !== false || strpos($host,'127.0.0.1') !== false ? true : false;
    }

    static function isValidCmsVersion()
    {
        include self::getWpIncludesPath().DS.'version.php';

        if(isset($wp_version))
        {
            $status = (bool)version_compare($wp_version,'4.0.0','>=');
            $msg = !$status ? 'امکان نصب پِی زیتو بر روی نسخه وردپرس کمتر از نسخه 4.0 وجود ندارد.' : '';

            return [$status,$msg];
        }
        else
        {
            return [true,''];
        }
    }

    /* Self */

    static function startSession()
    {
        static $start;

        if(!$start && !session_id() && !headers_sent())
        {
            session_start();
        }
    }

    static function modifyQuery($query)
    {
        global $wpdb;
        return str_replace('#__',$wpdb->prefix,$query);
    }

    static function getAllTables()
    {
        global $wpdb;
        $sql = "SHOW TABLES LIKE '%'";
        $results = $wpdb->get_results($sql);
        $tables = [];

        foreach($results as $index => $value)
        {
            foreach($value as $tableName)
            {
                $tables[] = $tableName;
            }
        }

        return $tables;
    }

    static function getTablePrefix()
    {
        global $wpdb;
        return $wpdb->prefix;
    }

    private static function getAssetsHash($src)
    {
        return 'pa-'.substr(md5($src),0,6);
    }

    static function isBackend()
    {
        if(self::isAjaxRequest())
        {
            return isset($_REQUEST['area']) && $_REQUEST['area'] == 'admin' ? true : false;
        }
        else
        {
            return is_admin();
        }
    }

    static function isAjaxRequest()
    {
        return (defined('DOING_AJAX') && DOING_AJAX) || !empty($_REQUEST['action']);
    }

    static function getAdminPath()
    {
        return self::getRootPath().DS.'wp-admin';
    }

    static function getUnzipDirectory()
    {
        return self::getRootPath().DS.'wp-content'.DS.'plugins';
    }

    static function getTempDirectory()
    {
        return self::getWpContentPath().DS.'payzitotmp';
    }

    static function getWpContentPath()
    {
        return self::getRootPath().DS.'wp-content';
    }

    static function getWpIncludesPath()
    {
        return self::getRootPath().DS.'wp-includes';
    }

    static function getCmsText()
    {
        return 'وردپرس';
    }

    static function getCmsSupportedVersions()
    {
        return [4];
    }

    static function getPhpSupportedVersions()
    {
        return [7.2];
    }

    static function isValidPhpVersion()
    {
        $status = version_compare(phpversion(),'7.2.0','>=');
        $msg = !$status ? 'امکان نصب پِی زیتو بر روی این نسخه php وجود ندارد. نسخه php سرور باید بالای 7.2.0 باشد.' : '';
        return [$status,$msg];
    }

    static function getOldTempDirectory()
    {
        return self::getTempDirectory();
    }

    static function getSetupSession($key=null)
    {
        $setup = get_option('payzito_setup_ready');
        $setup = !empty($setup) && is_array($setup) ? $setup : [];

        if(!is_null($key))
        {
            return isset($setup[$key]) ? $setup[$key] : false;
        }

        return $setup;
    }

    static function updateSetupSession($key,$value)
    {
        $data = self::getSetupSession();
        $data[$key] = $value;
        update_option('payzito_setup_ready',$data);
    }

    static function setSetupSession()
    {
        self::clearSetupSession();
        add_option('payzito_setup_ready',['start' => 1]);
    }

    static function clearSetupSession()
    {
        delete_option('payzito_setup_ready');
    }

    static function inSetupArea()
    {
        return self::getSetupSession();
    }
}