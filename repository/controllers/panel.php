<?php

namespace payzitoRepository\controllers;

use payzitoRepository\libraries\controller;
use payzitoRepository\libraries\helper;

class panel extends controller
{
    function __construct()
    {
        include_once PAYZITO_REPOSITORY_ROOT.DS.'setup'.DS.'setup.php';

        helper::loadLanguages();
    }

    function index()
    {
        $pluginData = get_plugin_data(PAYZITO_REPOSITORY_ROOT.'/../payzito.php', false, false);

        $chartData = [
            1 => [
                ['Element',helper::_('PA_COUNT'),['role' => 'style']],
            ],
            2 => [
                ['Element',helper::_('PA_AMOUNT'),['role' => 'style']],
            ],
            3 => [
                ['Element',helper::_('PA_COUNT'),['role' => 'style']],
            ],
            4 => [
                ['Element',helper::_('PA_AMOUNT'),['role' => 'style']],
            ],
        ];

        $cdns = [
            '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
            '//lib.arvancloud.com/ar/font-awesome/5.9.0/css/all.css',
            '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
            '//cdn.jsdelivr.net/fontawesome/4.7.0/css/font-awesome.min.css',
            '//use.fontawesome.com/c2e14644e4.js',
        ];

        $data = [
            1 => [22,25,15,36,8,22,29],
            2 => [225000,256000,350000,125000,452000,325000,292000],
            3 => [110,99,123,133],
            4 => [1546000,2450000,1425000,1758000],
        ];

        for($i=6;$i>=0;$i--)
        {
            $dateOne = helper::getDate('d F',time() - ($i+1)*24*60*60);
            $chartData[1][] = [$dateOne,$data[1][$i],'#2dbe00'];
            $chartData[2][] = [$dateOne,$data[2][$i],'#007dbe'];

            if($i < 4)
            {
                $dateTwo = helper::getDate('F Y',time() - ($i+1)*30*24*60*60);
                $chartData[3][] = [$dateTwo,$data[3][$i],'#2dbe00'];
                $chartData[4][] = [$dateTwo,$data[4][$i],'#007dbe'];
            }
        }

        $setupData = \PASetup::getSetupFileData();

        $installData = \PASetup::getInstallData('install');
        $installData['packages'] = [
            'pkg_payzito' => [
                'actions' => $setupData['actions'],
                'changelogs' => $setupData['changelogs'],
                'element' => 'pkg_payzito',
                'name' => 'پِی زیتو',
                'version' => $setupData['version'],
            ],
        ];

        $this->loadView('panel',compact('chartData','installData','pluginData','cdns'));
    }

    function download()
    {
        sleep(4);
        $result = [0,helper::_('PA_NOT_DOWNLOAD_PACKAGE')];
        die(json_encode($result,JSON_UNESCAPED_UNICODE));
    }
}