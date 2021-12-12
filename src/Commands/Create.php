<?php
namespace App\Console\Commands;

use App\Console;

class Create extends AbstractCommand
{
    public $name = 'Создание команды';
    public $description = 'Создает класс - обработчик команды';
    protected $path = '';

    function __construct(Console\Console $com)
    {
        parent::__construct($com);

        $this->console = $com;
    }

    public function execute()
    {
        $cName = $this->console->toCamel($this->args[0]);
        if(!preg_match('#^[\d\w]{3,20}$#i',$cName)){
            $this->echoR(
                'Имя команды '.$cName.' должно быть указано '
                .'латинским шрифтом от 3х до 20 символов'
            );
            return false;
        }

        // Есть ли название в списке уже имеющихся команд
        $this->path = $this->console->getPath();

        $listLib = $this->getTree(__DIR__);
        $listApp = $this->getTree($this->path);

        $list = array_merge($listApp, $listLib);

        if(in_array($cName, $list)){
            $this->echoR(
                'Команда '.$cName.' уже существует.'
            );
            return false;
        }

        if(!isset($this->args[1]) || !isset($this->args[2])){
            $this->echoR(
                'Не указаны наименование или описание команды.'
            );
            return false;
        }

        $res = $this->create($cName, $this->args[1], $this->args[2]);

        if($res) $this->echoG('Команда '.$cName.' успешно создана.');
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
                $res[] = preg_replace("#^([\w\d]+)\.php$#","$1",$sFile);
            }
        }
        closedir($dd);
        return $res;
    }

    private function create(string $class, string $name, string $desc)
    {
        $content = '<?php
namespace App\Console\Commands;

class '.$class.' extends AbstractCommand
{
    public $name = "'.$name.'";
    public $description = "'.$desc.'";

    public function execute()
    {

    }
}
        ';

        if(!is_dir($this->path))mkdir($this->path,0755, true);

        $filename = $this->path."/". $class.".php";

        if(!file_put_contents($filename, $content)){
            $this->echoR('Ошибка создания файла.');
            return false;
        }
        return true;
    }
}