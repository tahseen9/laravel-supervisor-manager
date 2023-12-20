<?php

namespace Tahseen9\LaravelSupervisorManager\Services;
use App\Models\SupervisorConfig;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use RuntimeException;
use Symfony\Component\Process\Process;

class ConfigurationManager
{

    protected string $dirPermission;
    protected string $filePermission;
    protected string $owner;
    protected string $group;
    protected array $defaultProgram;

    /**
     * Constructor for the class.
     * Retrieves relevant configuration values and assigns them to class properties.
     *
     * @return void
     */
    public function __construct()
    {
        $this->dirPermission = Config::get('laravel-supervisor-manager.config.permissions.dir'); // 0755
        $this->filePermission = Config::get('laravel-supervisor-manager.config.permissions.file'); // 0755
        $this->owner = Config::get('laravel-supervisor-manager.config.owner'); // www-data
        $this->group = Config::get('laravel-supervisor-manager.config.group'); // www-data
        $this->defaultProgram = Config::get('laravel-supervisor-manager.program.program_name'); // program_name

    }

    /**
     * Saves a configuration.
     *
     * @param array $config The configuration to save.
     * @param int|string|null $id The ID of the configuration to update. If null, a new configuration will be created.
     * @return bool Returns true if the configuration was successfully saved.
     */
    public function save(array $config, int|string $id = null): bool
    {
        // verify config keys from $this->defaultProgram
        $missingKeys = array_diff(array_keys($this->defaultProgram), array_keys($config));

        // if any key is missing add it through default config
        foreach ($missingKeys as $key) {
            $config[$key] = $this->defaultProgram[$key];
        }

        // take the program name and unset it from config array
        $programName = $config['program_name'];
        unset($config['program_name']);

        // prepare config array structure
        $config = [
            $programName => $config
        ];

        // save config in the table if it is new
        $modelMethod = 'create';

        // check if $id is present and is exists in SupervisorConfig model
        if(!is_null($id) && SupervisorConfig::where('id', $id)->exists()){
            // update if id is present
            $modelMethod = 'update';
        }

        SupervisorConfig::where('id', $id)->{$modelMethod}([
            'name' => $programName,
            'config' => $config,
            'file' => ''
        ]);

        return true;
    }

    /**
     * Deactivates a configuration by ID.
     *
     * @param int|string $id The ID of the configuration to deactivate.
     * @return bool Returns true if the configuration was successfully deactivated.
     * @throws RecordsNotFoundException If the given config ID does not exist in the SupervisorConfig Model.
     */
    public function deactivateConfig(int|string $id): bool
    {
        $supervisorConfig = SupervisorConfig::whereId($id);

        // if config exists in SupervisorConfig Model
        if(!$supervisorConfig->exists()){
            throw new RecordsNotFoundException("Given config id does not exists");
        }

        $supervisorConfig = $supervisorConfig->first();

        if (File::exists($supervisorConfig->path)) {
            File::delete($supervisorConfig->path);
        }

        return true;
    }

    /**
     * Activates a configuration by creating a file and setting its ownership and permissions.
     *
     * @param int|string $id The ID of the configuration to activate.
     *
     * @return bool Returns true if the activation is successful, false otherwise.
     * @throws RuntimeException if setting ownership or permissions fails.
     *
     * @throws RecordsNotFoundException if the configuration with the given ID does not exist.
     */
    public function activateConfig(int|string $id): bool
    {
        $supervisorConfig = SupervisorConfig::whereId($id);

        // if config exists in SupervisorConfig Model
        if(!$supervisorConfig->exists()){
            throw new RecordsNotFoundException("Given config id does not exists");
        }

        $supervisorConfig = $supervisorConfig->first();

        // call getEnvDir() to get dirPath
        $dirPath = $this->getEnvDir();

        // call arrayToConfigString and convert config attribute from SupervisorConfig Model to configString
        $configString = $this->arrayToConfString($supervisorConfig->config);

        // using dirPath concat the name attribute from SupervisorConfig for file name and
        $file = $dirPath . '/' . $supervisorConfig->name . '.conf';

        //check if file already exists
        if (File::exists($file)) {
            $this->cleanAndWriteToFile($file, $configString);
        }else{
            //  write configString in that file
            File::put($file, $configString);
        }

        // Change the owner and group of the file to www-data. This command may fail if PHP doesn't have enough permissions.
        $process = new Process(['chown', $this->owner.':'.$this->group, $file]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Failed to set ownership: ' . $process->getErrorOutput());
        }

        // Set the permissions of the file to 0755. This command may fail if PHP doesn't have enough permissions.
        $process = new Process(['chmod', $this->filePermission, $file]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Failed to set permissions: ' . $process->getErrorOutput());
        }

        // update the file path in SupervisorConfig Model
        $supervisorConfig->file_path = $file;
        $supervisorConfig->save();

        return true;
    }

    /**
     * Retrieves the environment directory path based on the current environment mode.
     *
     * @return string The absolute path of the environment directory.
     */
    public function getEnvDir(): string
    {
        // Get application's base path
        $basePath = base_path();

        // Get the env mode from configuration.
        $envMode = Config::get('app.env');

        // Create the directory under the base path
        $dirPath = $basePath . '/' . $envMode;

        if ($this->validateDirectory($dirPath)) {
            return realpath($dirPath);
        }

        return $this->makeEnvDir($dirPath);
    }

    /**
     * Creates a directory for environment files.
     *
     * @param string $dirPath The path where the directory will be created.
     *
     * @return string The realpath of the created directory.
     *
     * @throws RuntimeException If the permissions on the directory cannot be set.
     */
    private function makeEnvDir(string $dirPath): string
    {

        File::makeDirectory($dirPath, $this->dirPermission, true, true);

        // Use Symfony Process to run the chown command
        $process = new Process(['chown', $this->owner.':'.$this->group, $dirPath]);
        $process->run();

        // Ensure the command executed successfully
        if (!$process->isSuccessful()) {
            throw new RuntimeException('Failed to set permissions on directory: ' . $process->getErrorOutput());
        }

        return realpath($dirPath);
    }

    /**
     * Validates a directory based on the specified criteria.
     *
     * @param string $dirPath The path of the directory to validate.
     *
     * @return bool Returns true if the directory meets the specified criteria, false otherwise.
     */
    public function validateDirectory(string $dirPath): bool
    {
        // Check if the directory exists.
        if (!File::exists($dirPath) or !File::isDirectory($dirPath)) {
            return false;
        }

        // Check permissions of the directory.
        // This will return the permissions in octal representation.
        // Convert output to a string and parse it.
        $permission = substr(sprintf('%o', fileperms($dirPath)), -4)  == $this->dirPermission;

        // Check directory owner.
        $owner =  posix_getpwuid(fileowner($dirPath))['name'] == $this->owner;

        //Check directory group.
        $group = posix_getgrgid(filegroup($dirPath))['name'] == $this->group;

        if(!$permission or !$owner or !$group){
            return false;
        }

        return true;
    }

    /**
     * Converts an array to a configuration string.
     * The configuration string is formatted as an INI file.
     *
     * @param array $array The array to convert.
     *                     The keys represent sections in the configuration file,
     *                     and the values represent key-value pairs in each section.
     * @return string The configuration string.
     */
    private function arrayToConfString(array $array): string {
        $confString = '';

        foreach($array as $section => $configs) {
            $confString .= "[{$section}]\n";
            foreach($configs as $key => $value) {
                if (is_array($value)) {
                    foreach($value as $subKey => $subValue) {
                        if (is_bool($subValue)) {
                            $subValue = $subValue ? 'true' : 'false';
                        }
                        $confString .= "{$subKey}={$subValue}\n";
                    }
                } else {
                    if (is_bool($value)) {
                        $value = $value ? 'true' : 'false';
                    }
                    $confString .=  "{$key}={$value}\n";
                }
            }
            $confString .= "\n";
        }

        return $confString;
    }

    /**
     * Cleans the file at the specified path and writes the given content to it.
     * If the file already exists, its content will be cleared before writing the new content.
     *
     * @param string $filePath The path to the file.
     * @param string $content The content to be written to the file.
     * @return void
     */
    private function cleanAndWriteToFile(string $filePath, string $content):void {
        if (File::exists($filePath)) {
            File::put($filePath, ''); // Cleaning the existing content
        }

        File::append($filePath, $content);
    }
}
