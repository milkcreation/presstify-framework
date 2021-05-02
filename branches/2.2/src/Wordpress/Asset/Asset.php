<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Asset;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Support\Proxy\HttpRequestProxy;

class Asset
{
    use HttpRequestProxy;

    /**
     * Instance du gestionnaire d'asset.
     * @var AssetManagerInterface $asset
     */
    protected $asset;

    public function __construct(AssetManagerInterface $asset)
    {
        $this->asset = $asset;

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