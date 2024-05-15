<?php

namespace MicroProject;

use MicroProject\Loaders\ConfigLoader;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

final class App extends Application {

    private static ?App $instance = null;
    private static ?ConfigLoader $config = null;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$config = ConfigLoader::getInstance();
        }

        return self::$instance;
    }

    public function init(): void
    {
        $this->setCommands();
        $this->run();
    }

    private function setCommands(): void
    {
        if (self::$config->get('commands') === null ) {
            throw new \Exception("No commands config file found.");
        }

        foreach (self::$config->get('commands') as $command) {
            if (!class_exists($command) || !is_subclass_of($command, Command::class)) {
                echo "WARNING: Class {$command} does not exist or is not a subclass of Symfony\\Component\\Console\\Command\\Command.\n" . PHP_EOL;
                continue;
            }

            $this->add(new $command());
        }
    }

    /**
     * prevent the instance from being cloned (which would create a second instance of it)
     */
    private function __clone()
    {
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

}