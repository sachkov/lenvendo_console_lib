<?php
namespace sachkov\lenvendoconsolelib\Commands;

use sachkov\lenvendoconsolelib as clib;

abstract class AbstractCommand
{
    use clib\OutputTrait;

    protected $args = [];
    protected $params = [];
    public $name = '';
    public $description = '';

    function __construct(clib\Console $com)
    {
        $this->args = $com->getArgs();
        $this->params = $com->getParams();
    }

    abstract public function execute();
}