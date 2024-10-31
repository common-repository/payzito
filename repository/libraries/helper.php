<?php

namespace payzitoRepository\libraries;

use Gettext\Generator\MoGenerator;
use Gettext\Loader\PoLoader;
use Gettext\Translations;

defined('PAR_EXEC') OR die('Payzito Repository Restricted Access');

class helper
{
    static function getCorePath()
    {
        return PAYZITO_REPOSITORY_ROOT;
    }

    static function loadView($directory,$passParams=[],$return=false)
    {
        $file = self::getCorePath().DS.'views'.DS.trim(str_replace('/',DS,$directory),DS).'.php';

        if(file_exists($file))
        {
            if(!empty($passParams))
            {
                extract($passParams);
            }

            if($return)
            {
                ob_start();
                include $file;
                $output = ob_get_contents();
                ob_end_clean();

                return $output;
            }
            else
            {
                include $file;
            }
        }

        return '';
    }

    static function getDirectionClass()
    {
        return 'pa-' . self::getDirection();
    }

    static function getDirection()
    {
        $languageTag = self::getLanguageTag();
        return in_array($languageTag,self::getRtlLanguages()) ? 'rtl' : 'ltr';
    }

    static function getLanguageTag()
    {
        $locale = get_locale();
        return str_replace('_','-',($locale == 'en_US' ? 'en_GB' : $locale));
    }

    static function getRtlLanguages()
    {
        return ['fa-IR','ar-AA'];
    }

    static function imagesUri()
    {
        return self::assetsUri().'images/';
    }

    static function assetsUri()
    {
        return self::getRootUri().'wp-content/plugins/payzito/repository/assets/';
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

    static function _($keyword,$params=[])
    {
        $translate = __($keyword,'payzito');
        return empty($params) ? $translate : str_replace(array_keys($params),array_values($params),$translate);
    }

    static function getCurrency()
    {
        return self::_('PA_TOMAN');
    }

    static function getRootPath()
    {
        return rtrim(str_replace('/',DS,ABSPATH),DS);
    }

    static function loadLanguages()
    {
        $lang = self::getLanguageTag();

        self::createLanguageFiles($lang);

        load_plugin_textdomain('payzito');
    }

    static function createLanguageFiles($lang,$force=false)
    {
        $outputFile = self::getLanguageFile($lang,'po');
        if(!$force && file_exists($outputFile))
        {
            return;
        }

        $keywords = [];

        $file = self::getCorePath().DS.'languages'.DS.'payzito_'.$lang.'.ini';
        if(file_exists($file))
        {
            $keywords = array_merge($keywords,parse_ini_file($file));
        }

        $file = self::getCorePath().DS.'languages'.DS.'custom_'.$lang.'.ini';
        if(file_exists($file))
        {
            $keywords = array_merge($keywords,parse_ini_file($file));
        }

        $content = '';
        $content .= "#\n";
        $content .= "msgid \"\"\n";
        $content .= "msgstr \"\"\n";

        foreach ($keywords as $key => $value)
        {
            $content .= "\n";
            $content .= "msgid \"".$key."\"\n";
            $content .= "msgstr \"".addcslashes($value,'"')."\"\n";
        }

        self::createFolderIfNotExist(dirname($outputFile));

        self::writeFile($outputFile,$content);

        if(!file_exists($outputFile))
        {
            return;
        }

        require_once self::getCorePath().DS.'libraries'.DS.'mo-creator'.DS.'autoloader.php';

        $loader = new PoLoader();
        $translations = $loader->loadFile($outputFile);
        $generator = new MoGenerator();
        $generator->generateFile($translations,str_replace('.po','.mo',$outputFile));
    }

    static function getLanguageFile($lang,$ext='po')
    {
        return self::getRootPath().DS.'wp-content'.DS.'languages'.DS.'plugins'.DS.'payzito-'.str_replace('-','_',$lang).'.'.$ext;
    }

    static function writeFile($file,$content,$force=true)
    {
        if(!$force && file_exists($file))
        {
            return false;
        }

        self::createFolderIfNotExist(dirname($file));

        if(function_exists('file_put_contents'))
        {
            return file_put_contents($file,$content);
        }

        $handle = fopen($file,"w");
        if(!$handle)
        {
            return false;
        }

        $result = fwrite($handle,$content);
        fclose($handle);
        return $result;
    }

    static function createFolderIfNotExist($folder)
    {
        return !self::existFolder($folder) ? self::createFolder($folder) : true;
    }

    static function existFolder($folder)
    {
        return !empty($folder) ? is_dir(realpath($folder)) : false;
    }

    static function createFolder($path)
    {
        return !self::existFolder($path) ? mkdir($path,0777,true) : true;
    }

    static function getDate($format,$timestamp=null)
    {
        if(self::getLanguageTag() == 'fa-IR')
        {
            self::includeJdateLibrary();
            $jdate = new jdate();
            $date = $jdate->getJalaiDate($format,$timestamp);
            $date = $jdate->modifyDate($date);
            $date = $jdate->convertNumber($date,'fa');
            return $date;
        }
        else
        {
            return date($format,$timestamp);
        }
    }

    static function includeJdateLibrary()
    {
        static $loaded = false;

        if(!$loaded)
        {
            $loaded = true;
            include_once dirname(__FILE__).DS.'jdate.php';
        }
    }

    static function modifyNumber($number)
    {
        if(self::getLanguageTag() == 'fa-IR')
        {
            $jdate = new jdate();
            return $jdate->convertNumber($number,'fa','.');
        }

        return $number;
    }

    static function downloadFileFromUrl($url)
    {
        if(empty($url))
        {
            return [0,''];
        }

        include_once self::getAdminPath().DS.'includes'.DS.'file.php';

        $result = download_url($url,17);
        if(is_wp_error($result))
        {
            return [0,$result->get_error_message()];
        }
        if(empty($result))
        {
            return [0,''];
        }

        return [1,'',['file' => str_replace('/',DS,$result)]];
    }

    static function getAdminPath()
    {
        return self::getRootPath().DS.'wp-admin';
    }

    static function adminUri()
    {
        return self::getRootUri().'wp-admin/';
    }

    static function copyFile($src,$dst,$force=true)
    {
        if(!self::existFile($src))
        {
            return false;
        }
        if(!$force && self::existFile($dst))
        {
            return false;
        }

        self::createFolderIfNotExist(self::fileDirectory($dst));

        return @copy($src,$dst);
    }

    static function existFile($file)
    {
        return file_exists($file) && is_readable($file);
    }

    public static function readFile($file)
    {
        if(!self::existFile($file))
        {
            return null;
        }

        if(function_exists('file_get_contents'))
        {
            return file_get_contents($file);
        }

        $handle = fopen($file,"r");
        if(!$handle)
        {
            return false;
        }

        $contents = fread($handle,filesize($file));
        fclose($handle);
        return $contents;
    }

    static function fileDirectory($path)
    {
        return dirname($path);
    }

    static function modifyPath($path)
    {
        return rtrim(str_replace('/',DS,$path),DS);
    }

    static function deleteFolderIfExist($folder)
    {
        return self::existFolder($folder) ? self::deleteFolder($folder) : false;
    }

    static function deleteFolder($path)
    {
        $path = self::modifyPath($path);

        if(!self::existFolder($path))
        {
            return true;
        }

        $files = self::folderFiles($path);
        foreach (!empty($files) ? $files : [] as $file)
        {
            self::deleteFile($path.DS.$file);
        }

        $folders = self::folderFolders($path);
        foreach (!empty($folders) ? $folders : [] as $folder)
        {
            self::deleteFolder($path.DS.$folder);
        }

        @rmdir($path);

        return true;
    }

    static function folderFiles($path,$filter='.')
    {
        return self::folderFileOrFolders('files',$path,$filter);
    }

    static function folderFolders($path,$filter='.')
    {
        return self::folderFileOrFolders('folders',$path,$filter);
    }

    static function deleteFile($file)
    {
        return unlink($file);
    }

    private static function folderFileOrFolders($type,$path,$filter)
    {
        $path = self::modifyPath($path);

        if(!self::existFolder($path))
        {
            return [];
        }

        $files = [];
        foreach (glob($path.DS.'*') as $file)
        {
            if((($type == 'folders' && is_dir($file)) || ($type == 'files' && is_file($file))) && !in_array($file,['.','..']) && preg_match("/$filter/",$file))
            {
                $files[] = self::fileName($file);
            }
        }

        return $files;
    }

    static function fileName($path)
    {
        return basename($path);
    }

    static function getData($filename)
    {
        $file = self::getCorePath().DS.'data'.DS.$filename.'.json';
        $content = file_exists($file) ? self::readFile($file) : '';
        return !empty($content) ? (array) json_decode($content,true) : [];
    }

    public static function htmlEntities($input)
    {
        return htmlentities($input,ENT_COMPAT|ENT_QUOTES,null, true);
    }

    static function modifyPrice($num)
    {
        return number_format($num);
    }

    static function getAdminUrl($name)
    {
        return 'admin.php?page=payzito-'.$name;
    }

    static function modifyDomain($domain)
    {
        return str_replace(['https://','http://','www.'],'',$domain);
    }

    static function createAttr($attr)
    {
        if(empty($attr) || !is_array($attr))
        {
            return '';
        }

        $return = '';
        foreach ($attr as $key => $value)
        {
            $return .= $key.'="'.$value.'" ';
        }

        return trim($return);
    }

    public static function jsonEncode($array)
    {
        if(!is_array($array))
        {
            return [];
        }

        return json_encode($array,JSON_UNESCAPED_UNICODE);
    }

    public static function jsonDecode($string,$assoc=true)
    {
        if(!is_string($string))
        {
            return [];
        }

        $array = json_decode($string,$assoc);
        return (is_array($array) && !empty($array)) ? $array : [];
    }

    static function getJalaliDate($a,$b)
    {
        return '';
    }
}