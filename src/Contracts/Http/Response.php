<?php declare(strict_types=1);

namespace tiFy\Contracts\Http;

use Illuminate\Http\Response as LaraResponse;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use Psr\Http\Message\ResponseInterface;
use tiFy\Contracts\Http\Response as ResponseContract;

/**
 * Interface Response
 * @package tiFy\Contracts\Http
 *
 * @mixin LaraResponse
 */
interface Response
{
    /**
     * Convertion d'une instance de réponse en réponse HTTP Psr-7
     *
     * @param ResponseContract $request
     *
     * @return ResponseInterface|null
     */
    public static function convertToPsr(?SfResponse $response = null): ?ResponseInterface;

    /**
     * Création d'une instance depuis une réponse PSR-7.
     *
     * @param ResponseInterface $psrResponse
     *
     * @return static
     */
    public static function createFromPsr(ResponseInterface $psrResponse): SfResponse;
}