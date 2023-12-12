<?php

namespace Tahseen9\LaravelSupervisorManager\Services;

// Configuration file manager
class ConfigurationManager
{

    public function makeConfigDir(): string
    {
        // get application base path
        // get the env mode : production/dev/etc
        // create the directory of the same name
        // set permissions - www-data
        // return the created directory path
        return 'directory_path';
    }

    public function getConfigDir(): string
    {
        // get the application base path
        // get the env mode : production/dev/etc
        // check the directory is present - return empty if not
        // return directory path
        return 'directory_path';
    }

    public function save(array $config): string
    {
        // validate the config values
        // get the name from the config values and check if file already exists
        // use file if already exists and overwrite the config
        // if new, create file and write the config
        return 'saved config path';
    }

    public function remove(string|array $config): bool
    {
        // check if configuration is not running
        // stop if it is running
        // remove configuration mentioned in config
        // array: multiple, string single
        return true;
    }

    public function stub(): string
    {
        return 'stub';
    }

}
