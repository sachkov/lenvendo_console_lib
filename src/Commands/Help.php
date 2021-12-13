<?php
namespace sachkov\lenvendoconsolelib\Commands;

use sachkov\lenvendoconsolelib as clib;

class Help extends AbstractCommand
{
    public $name = 'Помощь';
    public $description = 'Отображает наименование и описание команды';

    function __construct(clib\Console $com)
    {
        parent::__construct($com);

        $this->console = $com;
    }

    public function execute()
    {
        if(!$this->args[0]){
            $this->echoR('Команда не найдена');
            print_r($this);
            return false;
        }
        // Поиск обработчика команды
        $internalClass = __NAMESPACE__.'\Commands\\'.$this->args[0];
        $externalClass = $this->console::APP_NAMESPACE.$this->args[0];
        // сначала ищем команду в библиотеке, потом в приложении
        if(class_exists($internalClass)){
            $command = new $internalClass($this->console);
        }elseif(class_exists($externalClass)){
            $command = new $externalClass($this->console);
        }else{
            $this->echoR('Команда не найдена.');
            return false;
        }

        $this->echoG($this->args[0].' - '.$command->name);
        $this->echoY($command->description);
    }
}