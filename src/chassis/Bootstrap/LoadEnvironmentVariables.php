<?php

namespace DaWaPack\Chassis\Bootstrap;

use DaWaPack\Chassis\Support\Env;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidEncodingException;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;

class LoadEnvironmentVariables
{
    /**
     * The directory containing the environment file.
     *
     * @var string
     */
    protected $filePath;

    /**
     * The name of the environment file.
     *
     * @var string|null
     */
    protected $fileName;

    /**
     * Create a new loads environment variables instance.
     *
     * @param string $path
     * @param string|null $name
     *
     * @return void
     */
    public function __construct($path, $name = null)
    {
        $this->filePath = $path;
        $this->fileName = $name;
    }

    /**
     * Setup the environment variables.
     *
     * If no environment file exists, we continue silently.
     *
     * @return void
     */
    public function bootstrap()
    {
        $e = null;
        try {
            $this->createDotenv()->load();
        } catch (InvalidFileException $e) {
            // Just catch this
        } catch (InvalidPathException $e) {
            // Just catch this
        } catch (InvalidEncodingException $e) {
            // Just catch this
        }
        if (! is_null($e)) {
            $this->writeErrorAndDie([$e->getMessage()]);
        }
    }

    /**
     * Create a Dotenv instance.
     *
     * @return Dotenv
     */
    protected function createDotenv()
    {
        return Dotenv::create(
            Env::getRepository(),
            $this->filePath,
            $this->fileName
        );
    }

    /**
     * Write the error information to the screen and exit.
     *
     * @param string[] $errors
     *
     * @return void
     */
    protected function writeErrorAndDie(array $errors)
    {
        $fileResource = fopen("php://stderr", "a");
        if ($fileResource !== false) {
            foreach ($errors as $error) {
                fwrite($fileResource, $error);
            }
            fclose($fileResource);
        }

        exit(1);
    }
}
