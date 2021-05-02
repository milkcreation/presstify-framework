<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Pollen\Asset\AssetInterface;
use Pollen\Asset\AssetManagerInterface;

/**
 * @method static AssetManagerInterface addGlobalJsVar(string $key, $value, bool $inFooter = false, ?string $namespace = 'app')
 * @method static AssetManagerInterface addInlineCss(string $css)
 * @method static AssetManagerInterface addInlineJs(string $css)
 * @method static AssetInterface[]|array all()
 * @method static AssetManagerInterface enableMinifyCss(bool $minify = true)
 * @method static AssetManagerInterface enableMinifyJs(bool $minify = true)
 * @method static bool exists()
 * @method static string footerScripts()
 * @method static AssetInterface|null get(string $name)
 * @method static string getBaseDir()
 * @method static string getBaseUrl()
 * @method static string getRelPrefix()
 * @method static string headerStyles()
 * @method static string headerScripts()
 * @method static AssetManagerInterface setAsset(string $name, string $path)
 * @method static AssetManagerInterface setBaseDir(string $baseDir)
 * @method static AssetManagerInterface setBaseUrl(string $baseUrl)
 * @method static AssetManagerInterface setManifestJson(string $manifestJson, callable $fallback = null)
 * @method static AssetManagerInterface setRelPrefix(string $relPrefix)
 */
class Asset extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return AssetManagerInterface
     */
    public static function getInstance(): AssetManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return AssetManagerInterface::class;
    }
}