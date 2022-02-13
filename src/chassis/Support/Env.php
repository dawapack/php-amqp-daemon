<?php

declare(strict_types=1);

namespace DaWaPack\Chassis\Support;

use Dotenv\Repository\Adapter\PutenvAdapter;
use Dotenv\Repository\RepositoryInterface;
use Dotenv\Repository\RepositoryBuilder;
use InvalidArgumentException;
use PhpOption\Option;
use function DaWaPack\Chassis\Helpers\value;

class Env
{
    /**
     * Indicates if the putenv adapter is enabled.
     *
     * @var bool
     */
    protected static $putenv = true;

    /**
     * The environment repository instance.
     *
     * @var RepositoryInterface|null
     */
    protected static $repository;

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public static function enablePutenv()
    {
        static::$putenv = true;
        static::$repository = null;
    }

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public static function disablePutenv()
    {
        static::$putenv = false;
        static::$repository = null;
    }

    /**
     * Get the environment repository instance.
     *
     * @return RepositoryInterface
     */
    public static function getRepository()
    {
        if (static::$repository === null) {
            $builder = RepositoryBuilder::createWithDefaultAdapters();

            if (static::$putenv) {
                $builder = $builder->addAdapter(PutenvAdapter::class);
            }

            static::$repository = $builder->immutable()->make();
        }

        return static::$repository;
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return Option::fromValue(static::getRepository()->get($key))
            ->map(function ($value) {
                try {
                    return self::getValueByHisType($value);
                } catch (InvalidArgumentException $reason) {
                    if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)) {
                        return $matches[2];
                    }
                }
                return $value;
            })
            ->getOrCall(function () use ($default) {
                return value($default);
            });
    }

    /**
     * @param $value
     *
     * @return bool|string|void
     *
     * @throws InvalidArgumentException
     */
    public static function getValueByHisType($value)
    {
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
            default:
                throw new InvalidArgumentException();
                break;
        }
    }
}
