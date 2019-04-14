<?php

namespace tiFy\Contracts\Http;

use Illuminate\Http\RedirectResponse as LaraRedirectResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface RedirectResponse
 * @package tiFy\Contracts\Http
 *
 * @mixin LaraRedirectResponse
 */
interface RedirectResponse
{
    /**
     * @inheritdoc
     */
    public static function createPsr(?string $url, int $status = 302, array $headers = []): ?ResponseInterface;
}