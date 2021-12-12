<?php
namespace App\Console;

trait OutputTrait
{
    protected $textColors = [
        'red'       => '0;31',
        'green'     => '0;32',
        'yellow'    => '1;33',
        'white'     => '1;37'
    ];

    protected function out(string $str='',string $color='white')
    {
        if(!$str) return null;
        $colorNum = $this->textColors[$color]??'1;37';
        $res = "\033[".$colorNum."m";
        $res .= $str."\033[0m".PHP_EOL;
        echo $res;
    }

    public function echoR($str){ $this->out($str, 'red');}

    public function echoRed($str){ $this->out($str, 'red');}
    public function echoY($str){ $this->out($str, 'yellow');}
    public function echoYellow($str){ $this->out($str, 'yellow');}
    public function echoG($str){ $this->out($str, 'green');}
    public function echoGreen($str){ $this->out($str, 'green');}
}