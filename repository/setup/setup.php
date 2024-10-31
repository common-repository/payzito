<?php

defined('DS') OR define('DS',DIRECTORY_SEPARATOR);

if(defined('JPATH_ROOT'))
{
    require_once dirname(__FILE__) .DS. 'cms' .DS. 'joomla' .DS. 'setup.php';
    class PASetupParent extends PASetupJoomla{}
}
elseif(defined('ABSPATH'))
{
    require_once dirname(__FILE__) .DS. 'cms' .DS. 'wordpress' .DS. 'setup.php';
    class PASetupParent extends PASetupWordpress{}
}

class PASetupGeneral extends PASetupParent
{
    public static function indexArray($arr,$index='id',$val='')
    {
        if(empty($arr))
        {
            return [];
        }

        $return = [];
        foreach ($arr as $value)
        {
            if(!key_exists($value[$index],$return))
            {
                if(!empty($val) && $val != $value[$index])
                {
                    continue;
                }
                $return[$value[$index]] = $value;
            }

        }

        return $return;
    }

    public static function writeFile($file,$content,$force=true)
    {
        if(!$force && file_exists($file))
        {
            return false;
        }

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

    static function modifyPath($path)
    {
        return rtrim(str_replace('/',DS,$path),DS);
    }

    static function existFolder($folder)
    {
        return is_dir(realpath($folder));
    }

    public static function createFolderIfNotExist($folder)
    {
        return !self::existFolder($folder) ? self::createFolder($folder) : true;
    }

    static function createFolder($path)
    {
        return !self::existFolder($path) ? mkdir($path,0777,true) : true;
    }

    static function folderFiles($path,$filter='.')
    {
        return self::folderFileOrFolders('files',$path,$filter);
    }

    static function folderFolders($path,$filter='.')
    {
        return self::folderFileOrFolders('folders',$path,$filter);
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
            if((($type == 'folders' && is_dir($file)) || ($type == 'files' && is_file($file))) && !in_array($file,['.','..']) &&  preg_match("/$filter/",$file))
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

    static function copyFolder($src,$dst,$force=true)
    {
        $src = self::modifyPath($src);
        $dst = self::modifyPath($dst);

        if(!self::existFolder($src) || (!$force && self::existFolder($dst)))
        {
            return false;
        }

        self::createFolderIfNotExist($dst);

        $files = self::folderFiles($src);
        foreach (!empty($files) ? $files : [] as $file)
        {
            @copy($src.DS.$file,$dst.DS.$file);
        }

        $folders = self::folderFolders($src);
        foreach (!empty($folders) ? $folders : [] as $folder)
        {
            self::copyFolder($src.DS.$folder,$dst.DS.$folder);
        }

        return true;
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

    static function existFile($file)
    {
        return file_exists($file) && is_readable($file);
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

    public static function deleteFile($file)
    {
        return unlink($file);
    }

    public static function deleteFileIfExist($file)
    {
        return self::existFile($file) ? self::deleteFile($file) : true;
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

    static function fileDirectory($path)
    {
        return dirname($path);
    }

    static function getCms()
    {
        $cms = null;
        if(defined('JPATH_ROOT'))
        {
            $cms = 'joomla';
        }
        elseif(defined('ABSPATH'))
        {
            $cms = 'wordpress';
        }

        return $cms;
    }
}

class PASetup extends PASetupGeneral
{
    static function getInstallData($process=null)
    {
        $fileData = self::getSetupFileData();

        if(is_null($process))
        {
            $process = self::tableExist('#__payzito_setting') && self::existAtLeastRow('#__payzito_setting',"`name`='addon_version'") ? 'update' : 'install';
        }

        $cmsSupportedVersions = self::getCmsSupportedVersions();
        $phpSupportedVersions = self::getPhpSupportedVersions();

        $tempContent = self::readFile( dirname(__FILE__).DS.'temp.php');
        $tempContent = preg_replace('/\s{2,}/', ' ', $tempContent);
        $tempContent = str_replace('{CMS}',self::getCmsText(),$tempContent);
        $tempContent = str_replace('{CMS_SUPPORT_VERSION}',implode(' و ',$cmsSupportedVersions),$tempContent);
        $tempContent = str_replace('{CMS_CURRENT_VERSION}',$cmsSupportedVersions[0],$tempContent);
        $tempContent = str_replace('{PHP_SUPPORT_VERSION}',implode(' و ',$phpSupportedVersions),$tempContent);
        $tempContent = str_replace('{PHP_CURRENT_VERSION}',$phpSupportedVersions[0],$tempContent);
        $tempContent = str_replace('{PANEL_URL}',self::getBackendUrl('panel'),$tempContent);
        $tempContent = str_replace('{TYPE}',($fileData['type'] == 'pro' ? 'حرفه‌ای' : ($fileData['type'] == 'biz' ? 'تجاری' : ($fileData['type'] == 'free' ? 'رایگان' : '###'))),$tempContent);

        return [
            'language' => 'fa-IR',
            'process' => $process,
            'area' => 'ex',
            'type' => $fileData['type'],
            'requestUrl' => self::getAdminUri().'index.php',
            'sampleAction' => json_encode(self::getSampleAction(),JSON_UNESCAPED_UNICODE),
            'domain' => self::getDomain(),
            'verifiedAccount' => false,
            'tempContent' => $tempContent,
        ];
    }

    static function getSetupFileData()
    {
        $fileData = self::readFile(dirname(__FILE__).DS.'cms'.DS.self::getCms().DS.'setup.json');
        $fileData = !empty($fileData) ? json_decode($fileData,true) : [];

        return array_merge([
            'version' => '',
            'type' => '',
            'changelogs' => '',
            'actions' => '',
        ],$fileData);
    }
}

interface PASetupImplements
{
    static function addScript($dir);
    static function addScriptDeclaration($content);
    static function addStyle($dir);
    static function getPluginPath($data);
    static function getAdminUri($pathOnly=false);
    static function getBasePath();
    static function getRootPath();
    static function getCorePath();
    static function getRootUri($pathOnly=false);
    static function getBackendUrl($src);
    static function setSession($name,$value);
    static function getSession($name);
    static function clearSession($name);
    static function getSampleAction();
    static function inInstallArea();
    static function enableInstallerPlugin();
    static function getMediaPath();
    static function moveMediaFiles();
    static function moveCoreFiles();
    static function movePackageFiles();
    static function dbQuery($query);
    static function getDomain();
    static function tableExist($table);
    static function existAtLeastRow($table,$where='');
    static function getCountOfTable($table,$where='');
    static function isLocalHost();
    static function isValidCmsVersion();
    static function getUnzipDirectory();
    static function getTempDirectory();
    static function getCmsText();
    static function getCmsSupportedVersions();
    static function getPhpSupportedVersions();
    static function isValidPhpVersion();
    static function getOldTempDirectory();
    static function getSetupSession($key=null);
    static function setSetupSession();
    static function updateSetupSession($key,$value);
    static function clearSetupSession();
    static function inSetupArea();
}