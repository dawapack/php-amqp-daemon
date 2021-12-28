<?php
declare(strict_types=1);

namespace DaWaPack\Chassis\Classes\Config;

use DaWaPack\Chassis\Exceptions\ConfigurationException;
use League\Config\Configuration as LeagueConfiguration;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;

class Configuration implements ConfigurationInterface
{

    private const LOGGER_COMPONENT_PREFIX = 'application_configuration';
    private const CONFIG_PATH = 'config';
    private const SCHEMAS_CONFIG_PATH = 'config/Schemas';

    private string $basePath;

    private LeagueConfiguration $configuration;

    private LoggerInterface $logger;

    /**
     * Configuration constructor.
     *
     * @param LeagueConfiguration $configuration
     * @param LoggerInterface $logger
     * @param string $basePath
     * @param array $aliases
     */
    public function __construct(
        LeagueConfiguration $configuration,
        LoggerInterface $logger,
        string $basePath,
        array $aliases = []
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->basePath = $basePath;
        // autoload aliases
        if (!empty($aliases)) {
            $this->load($aliases);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        // try to autoload alias
        $alias = $this->getAliasFromKey($key);
        if (!$this->configuration->exists($alias)) {
            $this->load($alias);
        }
        // return key if exists
        return $this->configuration->exists($key)
            ? $this->configuration->get($key)
            : null;
    }

    /**
     * @param array|string $alias
     */
    public function load($alias): void
    {
        if (!is_array($alias)) {
            $this->loadConfiguration($alias);
            return;
        }
        foreach ($alias as $fileAlias) {
            $this->loadConfiguration($fileAlias);
        }
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getAliasFromKey(string $key): string
    {
        return explode(".", $key)[0];
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    private function loadConfiguration(string $fileName): void
    {
        if ($this->configuration->exists($fileName)) {
            return;
        }
        try {
            $schemas = $this->getSchema($fileName)->getMethod('getSchema')->invoke(null);
            $definitions = $this->getDefinitions($fileName);
            // Add schema
            $this->configuration->addSchema($fileName, $schemas);
            // Add definitions
            $this->configuration->merge([$fileName => $definitions]);
        } catch (ReflectionException $reason) {
            $this->logger->error(
                $reason->getMessage(),
                [
                    "component" => self::LOGGER_COMPONENT_PREFIX . "_error",
                    "error" => $reason
                ]
            );
        } catch (ConfigurationException $reason) {
            $this->logger->error(
                $reason->getMessage(),
                [
                    "component" => self::LOGGER_COMPONENT_PREFIX . "_error",
                    "error" => $reason
                ]
            );
        }
    }

    /**
     * @param string $fileName
     *
     * @return ReflectionClass
     * @throws ReflectionException
     * @throws ConfigurationException
     */
    private function getSchema(string $fileName): ReflectionClass
    {
        // check schema file
        $shortClassName = ucfirst($fileName);
        $schemaFilePath = $this->getFilePath($shortClassName, self::SCHEMAS_CONFIG_PATH);
        if (!file_exists($schemaFilePath)) {
            throw new ConfigurationException("schema for alias '$shortClassName' not found");
        }
        return new ReflectionClass($this->extractNamespace($schemaFilePath, $shortClassName));
    }

    /**
     * @param string $filePath
     * @param string $shortClassName
     *
     * @return string
     * @throws ConfigurationException
     */
    private function extractNamespace(string $filePath, string $shortClassName): string
    {
        $content = file_get_contents($filePath);
        if (preg_match('#(namespace)(\\s+)([A-Za-z0-9\\\\]+?)(\\s*);#sm', $content, $matches) <= 0) {
            throw new ConfigurationException("namespace in '$shortClassName' not found");
        }
        return $matches[3] . '\\' . $shortClassName;
    }

    /**
     * @param string $fileName
     *
     * @return array
     * @throws ConfigurationException
     */
    private function getDefinitions(string $fileName): array
    {
        $definitionsFilePath = $this->getFilePath($fileName, self::CONFIG_PATH);
        // check definitions file
        if (!file_exists($definitionsFilePath)) {
            throw new ConfigurationException("definitions file for alias '$fileName' not found");
        }
        return require_once $definitionsFilePath;
    }

    /**
     * @param string $fileName
     * @param string $path
     *
     * @return string
     */
    private function getFilePath(string $fileName, string $path): string
    {
        return sprintf("%s/%s/%s.php", $this->basePath, $path, $fileName);
    }

}
