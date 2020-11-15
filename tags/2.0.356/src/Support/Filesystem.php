<?php declare(strict_types=1);

namespace tiFy\Support;

use BadMethodCallException;
use Exception;
use Composer\Util\Filesystem as ComposerFs;
use League\Flysystem\Util as BaseFilesystem;

/**
 * @method static bool remove(string $file)
 * @method static bool isDirEmpty(string $dir)
 * @method static bool emptyDirectory(string $dir, bool $ensureDirectoryExists = true)
 * @method static bool removeDirectory(string $directory)
 * @method static bool removeDirectoryPhp(string $directory)
 * @method static void ensureDirectoryExists(string $directory)
 * @method static bool unlink(string $path)
 * @method static bool rmdir(string $path)
 * @method static bool copyThenRemove(string $source, string $target)
 * @method static bool copy(string $source, string $target)
 * @method static bool rename(string $source, string $target)
 * @method static string findShortestPath(string $from, string $to, bool $directories = false)
 * @method static string findShortestPathCode(string $from, string $to, bool $directories = false, bool $staticCode = false)
 * @method static bool isAbsolutePath(string $path)
 * @method static int size(string $path)
 * @method static bool isLocalPath(string $path)
 * @method static bool relativeSymlink(string $target, string $link)
 * @method static bool isSymlinkedDirectory(string $directory)
 * @method static void junction(string $target, string $junction)
 * @method static bool isJunction(string $junction)
 * @method static bool removeJunction(string $junction)
 * @method static int|false filePutContentsIfModified(string $path, $content)
 * @method static void safeCopy(string $source, string $target)
 * @method static bool filesAreEqual(string $a, string $b)
 */
class Filesystem extends BaseFilesystem
{
    /**
     * Instance du gestionnaire de système de fichier de Composer.
     * @var ComposerFs|null
     */
    protected static $composer;

    /**
     * Délégation d'appel des méthodes du système de fichier de composer.
     *
     * @param string string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        try {
            $composerFs = static::composerFs();
            return $composerFs->{$method}(...$parameters);
        } catch (Exception $e) {
            throw new BadMethodCallException(sprintf(
                'ComposerFilesystem method [%s] call return exception >> [%s]', $method, $e->getMessage()
            ));
        }
    }

    /**
     * Récupération du gestionnaire de système de fichier de composer.
     *
     * @return ComposerFs
     */
    protected static function composerFs(): ComposerFs
    {
        if (is_null(static::$composer)) {
            static::$composer = new ComposerFs();
        }

        return static::$composer;
    }

    /**
     * @inheritDoc
     */
    public static function normalizePath($path)
    {
        return static::composerFs()->normalizePath($path);
    }
}