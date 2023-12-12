<?php

namespace Tahseen9\LaravelSupervisorManager\Services;

use Illuminate\Support\Facades\Process;

class ProcessManager
{
    public function startAll(): string
    {
        $result = Process::run('sudo supervisorctl start all');

        if($result->failed()){
            return $result->errorOutput();
        }

        return $result->output();
    }

    public function restartAll(): string
    {
        $result = Process::run('sudo supervisorctl start all');

        if($result->failed()){
            return $result->errorOutput();
        }

        return $result->output();
    }

    public function stopAll(): string
    {
        return 'console output';
    }

    public function start(string $name): string
    {
        return 'console output';
    }

    public function restart(string $name): string
    {
        return 'console output';
    }
    public function stop(string $name): string
    {
        return 'console output';
    }


}
