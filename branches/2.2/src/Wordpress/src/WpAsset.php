<?php

declare(strict_types=1);

namespace tiFy\Wordpress;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\HttpRequestProxy;
use Psr\Container\ContainerInterface as Container;

class WpAsset
{
    use ContainerProxy;
    use HttpRequestProxy;

    /**
     * @var AssetManagerInterface $asset
     */
    protected $asset;

    /**
     * @param AssetManagerInterface $asset
     * @param Container $container
     */
    public function __construct(AssetManagerInterface $asset, Container $container)
    {
        $this->asset = $asset;
        $this->setContainer($container);

        $this->asset
            ->setBaseDir(ABSPATH)
            ->setBaseUrl(site_url('/'))
            ->setRelPrefix($this->httpRequest()->getRewriteBase());

        $this->asset->addGlobalJsVar('abspath', ABSPATH);
        $this->asset->addGlobalJsVar('url', site_url('/'));
        $this->asset->addGlobalJsVar('rel', $this->httpRequest()->getRewriteBase());

        global $locale;
        $this->asset->addGlobalJsVar('locale', $locale);

        add_action(
            'wp_head',
            function () {
                echo $this->asset->headerStyles();
                echo $this->asset->headerScripts();
            },
            5
        );

        add_action(
            'wp_footer',
            function () {
                echo $this->asset->footerScripts();
            },
            5
        );

        add_action(
            'admin_print_styles',
            function () {
                echo $this->asset->headerStyles();
            }
        );

        add_action(
            'admin_print_scripts',
            function () {
                echo $this->asset->headerScripts();
            }
        );

        add_action(
            'admin_print_footer_scripts',
            function () {
                echo $this->asset->footerScripts();
            }
        );
    }
}