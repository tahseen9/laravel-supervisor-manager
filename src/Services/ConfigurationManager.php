<?php

namespace Tahseen9\LaravelSupervisorManager\Services;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;

// Configuration file manager
class ConfigurationManager
{
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
        // Get the config values
        $configValues = Config::get('app');

        // Validate the config values
        if (!isset($configValues['name']) || empty($configValues['name'])) {
            throw new \InvalidArgumentException('Invalid config values.');
        }

        // Get the name from the config values
        $configFileName = $configValues['name'];

        // Check if file already exists
        $configFilePath = base_path($configFileName);

        if (File::exists($configFilePath)) {
            // If file already exists, overwrite with the config values
            File::put($configFilePath, json_encode($configValues));
        } else {
            // If new, create file and write the config values
            File::put($configFilePath, json_encode($configValues));
        }
    }

    public function remove(string|array $config): bool
    {
        // check if configuration is not running
        // stop if it is running
        // remove configuration mentioned in config
        // array: multiple, string single
        return true;
    }

    private function arrayToIniFile($array, $filePath) {
        $iniString = '';

        foreach($array as $section => $configs) {
            $iniString .= "[{$section}]\n";
            foreach($configs as $key => $value) {
                if (is_array($value)) {
                    foreach($value as $subKey => $subValue) {
                        if (is_bool($subValue)) {
                            $subValue = $subValue ? 'true' : 'false';
                        }
                        $iniString .= "{$subKey}={$subValue}\n";
                    }
                } else {
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }
                    $iniString .=  "{$key}={$value}\n";
                }
            }
            $iniString .= "\n";
        }

        file_put_contents($filePath, $iniString);

        // Change the owner and group of the file to www-data. This command may fail if PHP doesn't have enough permissions.
        $process = new Process(['chown', 'www-data:www-data', $filePath]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Failed to set ownership: ' . $process->getErrorOutput());
        }

        // Set the permissions of the file to 0755. This command may fail if PHP doesn't have enough permissions.
        $process = new Process(['chmod', '0755', $filePath]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Failed to set permissions: ' . $process->getErrorOutput());
        }

        return $filePath;
    }

    private function makeEnvDir(): string
    {
        // Get application's base path
        $basePath = base_path();

        // Get the env mode from configuration.
        $envMode = Config::get('app.env');

        // Create the directory under the base path
        $dirPath = $basePath . '/' . $envMode;

        if (!File::exists($dirPath)) {
            File::makeDirectory($dirPath, 0755, true, true);
        }

        // Use Symfony Process to run the chown command
        $process = new Process(['chown', 'www-data:www-data', $dirPath]);
        $process->run();

        // Ensure the command executed successfully
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Failed to set permissions on directory: ' . $process->getErrorOutput());
        }

        return $dirPath;
    }

    function checkDirectory($directoryPath): bool
    {
        // Check if the directory exists.
        if (!File::isDirectory($directoryPath)) {
            echo "Directory does not exist.\n";
            return false;
        }

        // Check permissions of the directory.
        // This will return the permissions in octal representation.
        // To interpret this, you might want to convert it to a string and parse it.
        // But here, we are just printing the output.
        $permissions = substr(sprintf('%o', fileperms($directoryPath)), -4);
        echo "Directory permissions: {$permissions}\n";

        // Check directory owner.
        $ownerId = fileowner($directoryPath);
        $ownerInfo = posix_getpwuid($ownerId);
        $ownerName = $ownerInfo['name'];
        echo "Directory owner: {$ownerName}\n";

        //Check directory group.
        $groupId = filegroup($directoryPath);
        $groupInfo = posix_getgrgid($groupId);
        $groupName = $groupInfo['name'];
        echo "Directory group: {$groupName}\n";

        return true;
    }
}
