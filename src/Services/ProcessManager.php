<?php

namespace Tahseen9\LaravelSupervisorManager\Services;

use Illuminate\Support\Facades\Process;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class ProcessManager
{
    private array $functions;
    private array $commands;

    public function __construct()
    {
        $this->commands = config('laravel-supervisor-manager.commands');
        $this->functions = array_keys($this->commands);
    }

    public function __call($method, $args)
    {
        if(!in_array($method,$this->functions)){
            throw new MethodNotAllowedException($this->functions);
        }

        $command = $this->commands[$method];

        if(!empty($args)){
            $command = str_replace('{name}',$args[0], $command);
        }

        $result = Process::run($command);

        if($result->failed()){
            return $result->errorOutput();
        }

        return $result->output();

    }

}
