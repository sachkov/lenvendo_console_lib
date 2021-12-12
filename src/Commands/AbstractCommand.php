<?php
namespace App\Console\Commands;

use App\Console;

abstract class AbstractCommand
{
    use Console\OutputTrait;

    protected $args = [];
    protected $params = [];
    public $name = '';
    public $description = '';

    function __construct(Console\Console $com)
    {
        $this->args = $com->getArgs();
        $this->params = $com->getParams();
    }

    abstract public function execute();
}