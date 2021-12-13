<?php
namespace sachkov\lenvendoconsolelib\Commands;

use sachkov\lenvendoconsolelib as clib;

class Describe extends AbstractCommand
{
    private $console;
    public $name = 'Описание команд';
    public $description = 'Отображает наименование и описание всех команд.';

    function __construct(clib\Console $com)
    {
        parent::__construct($com);

        $this->console = $com;
    }
    
    public function execute()
    {
        $path = $this->console->getPath();

        $listLib = $this->getTree(__DIR__);
        $listApp = $this->getTree($path);

        $lib = array_merge($listApp, $listLib);

        print_r($lib);
    }

    private function getTree($path)
    {
        $res = [];
        $dd = opendir($path);
        while($sFile = readdir($dd)){
            if($sFile=='.' || $sFile=='..')continue;
            if(is_dir($path."/".$sFile)){
                $res = array_merge(
                    $res
                    ,$this->getTree($path."/".$sFile)
                );
            }elseif(preg_match("#\.php$#",$sFile)){
                $className = preg_replace("#^([\w\d]+)\.php$#","$1",$sFile);
                if($className == 'AbstractCommand') continue;
                $res[$className] = [
                    "name"=>$this->getProperties($path."/".$sFile, 'name'),
                    "desc"=>$this->getProperties($path."/".$sFile, 'description')
                ];
            }
        }
        closedir($dd);
        return $res;
    }

    /**
     * Вытащить свойства можно так же создав объекты или используя
     * reflection но показалось что так более производительно
     */
    private function getProperties($sFilename, $type)
    {
        $sData = file_get_contents($sFilename);
        $sData = str_replace("\n","{{break}}",$sData);
        if(!preg_match_all(
            "#\{\{break\}\}\s*public\s*.".$type."\s*\=\s*[\'\"]([\d\w\s\_\.\-]*)[\'\"]\s*\;#u",
            $sData,
            $m
        )) return '';
        
        return $m[1][0]??'';
    }
}