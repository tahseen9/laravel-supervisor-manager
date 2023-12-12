<?php

return [

    "commands" => [
        "start_all"         => "sudo supervisorctl start all",
        "restart_all"       => "sudo supervisorctl restart all",
        "stop_all"          => "sudo supervisorctl stop all",
        "status_all"        => "sudo supervisorctl status all",
        "start"             => "sudo supervisorctl start {name}:*",
        "restart"           => "sudo supervisorctl restart {name}:*",
        "stop"              => "sudo supervisorctl stop {name}:*",
        "status"              => "sudo supervisorctl status {name}:*",
    ]
];
