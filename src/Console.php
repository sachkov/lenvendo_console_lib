<?php
namespace App\Console;

class Console
{
    use OutputTrait;

    protected string $scriptName = '';
    protected string $commandName = 'Describe';
    protected array $arguments = [];
    protected array $params = [];
    protected array $errors = [];
    public $handler;                //обработчик команды

    // Неймспейс для команд приложения
    const APP_NAMESPACE = 'App\Console\Commands\\';
    // Путь для команд приложения
    protected $path = '';

    function __construct($root)
    {
        // Предполагаем что вызванный в консоли файл лежит в корне проекта
        $basePath = $root."/app/Console/Commands/";
        
        $this->path = realpath($basePath);
    }

    /**
     * Разбивка содержимого переданных скрипту аргументов
     */
    public function parse(array $command=[])
    {
        if(empty($command)) return [];

        foreach($command as $num=>$val){
            // находим имя вызванного скрипта
            $str = trim($val);
            if($num == 0){
                $pos = strrpos($str,'/');
                $this->scriptName = ($pos === false)
                        ?$str
                        :substr($str, ($pos+1));
                continue;
            }
            // имя команды
            if($num == 1){
                //if(!$str) continue;
                $str = $this->validateString($str);
                $this->commandName = $this->toCamel($str);
                continue;
            }

            //аргументы
            if($str[0] == '{' && substr($str, -1) == '}'){
                $str = substr($str, 1, -1);
                $this->arguments[] = $this->validateString($str);
                continue;
            }

            // параметры
            if(preg_match('/^\[(.*)=(.*)\]$/',$str,$m)){
                $key = $this->validateString($m[1]);
                $val = $this->validateString($m[2]);
                if(!isset($this->params[$key])) $this->params[$key] = [];
                $this->params[$key][] = $val;
                continue;
            }

            // Ошибки сиснтаксиса просто для примера
            if( $str[0] == '{' || substr($str, -1) == '}'
                || $str[0] == '[' || substr($str, -1) == ']'
            ){
                $this->errors[] = 'Нарушен синтаксис командной строки.';
                continue;
            }

            // оставшиеся аргументы
            $this->arguments[] = $this->validateString($str);
        }

        if($this->arguments[0] == 'help'){
            $this->arguments = [0=>$this->commandName];
            $this->commandName = 'Help';
        }
    }

    public function handle()
    {
        // Поиск обработчика команды
        $internalClass = __NAMESPACE__.'\Commands\\'.$this->commandName;
        $externalClass = self::APP_NAMESPACE.$this->commandName;
        // сначала ищем команду в библиотеке, потом в приложении
        if(class_exists($internalClass)){
            $command = new $internalClass($this);
        }elseif(class_exists($externalClass)){
            $command = new $externalClass($this);
        }else{
            $this->echoR('Команда '.$this->commandName.' не найдена.');
            return false;
        }

        $command->execute();
    }

    public function getArgs():array
    {
        return $this->arguments;
    }

    public function getParams():array
    {
        return $this->params;
    }

    public function getPath():string
    {
        return $this->path;
    }

    public function hasErrors()
    {
        if(count($this->errors)) return true;
        return false;
    }

    public function getErrors():string
    {
        $res = '';
        foreach($this->errors as $k=>$val){
            $res .= $k.") ".$val.";".PHP_EOL;
        }
        return $res;
    }

    /**
     * Перевод snake case to camel case
     * @param string параметр запроса
     * @return string очищенная строка
     */
    public function toCamel(string $str):string
    {
        $str = ucwords(strtolower($str),"_");
        return str_replace('_', '', $str);
    }

    /**
     * Валидация входящей строки
     * @param string параметр запроса
     * @return string очищенная строка
     */
    private function validateString(string $str):string
    {
        return filter_var($str, FILTER_SANITIZE_STRING);
    }

}