<?php

namespace MicroProject\Loaders;

class ConfigLoader {

    private static ?ConfigLoader $instance = null;
    private array $config = [];

    // Private constructor to prevent creating a new instance with 'new'
    private function __construct() {
        $this->loadConfig();
    }

    // Static method to get the single instance of the class
    public static function getInstance(): ConfigLoader 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfig(): void 
    {
        $configDirectory = CONFIGS_DIR;

        foreach (glob($configDirectory . DIRECTORY_SEPARATOR .'*.php') as $filepath) {
            $configArray = include $filepath;

            if (is_array($configArray)) {
                $filename = pathinfo($filepath, PATHINFO_FILENAME);
                $this->config[$filename] = $configArray;
            }
        }
    }

    // Get a configuration value by key
    public function get($key): array|null 
    {
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }
}