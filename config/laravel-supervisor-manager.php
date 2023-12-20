<?php

return [
    "config" => [
        "owner" => "www-data",
        "group" => "www-data",
        "permissions" => [
            "dir" => "0755",
            "file" => "0755"
        ]
    ],
    "program" => [ # default supervisor program structure
        "program_name" => [ # program name to identify the program group e.g. barcode-generator
            "process_name"  => "%(program_name)s_%(process_num)02d", # process name to identify the process in a group
            "command"       => ":command", # given command to execute in supervisor
            "autostart"     => true, # auto start the program whenever supervisor starts
            "autorestart"   => true, # auto restart if supervisor exits unexpectedly
            "user"          => "www-data", # default user
            "numprocs"      => 1, # default num process
            "redirect_stderr" => true, # it will send the error output to the same location as the standard output
            "stdout_logfile" => "{laravel-log-dir}/{program_name}_{env-name}_worker.log",
        ],
    ],
    "commands" => [
        "start_all"         => "sudo supervisorctl start all",
        "restart_all"       => "sudo supervisorctl restart all",
        "stop_all"          => "sudo supervisorctl stop all",
        "status_all"        => "sudo supervisorctl status all",
        "start"             => "sudo supervisorctl start {name}:*",
        "restart"           => "sudo supervisorctl restart {name}:*",
        "stop"              => "sudo supervisorctl stop {name}:*",
        "status"            => "sudo supervisorctl status {name}:*",
    ],

];
