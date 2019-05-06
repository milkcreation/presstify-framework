<?php declare(strict_types=1);

namespace tiFy\Http;

use Illuminate\Http\Response as LaraResponse;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use tiFy\Contracts\Http\Response as ResponseContract;

class Response extends LaraResponse implements ResponseContract
{
    /**
     * @inheritdoc
     */
    public static function convertToPsr(?SfResponse $response = null): ?ResponseInterface
    {
        if ($response = $response ?: new static()) {
            $psr17Factory = new Psr17Factory();
            $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

            return $psrHttpFactory->createResponse($response);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return ResponseContract
     */
    public static function createFromPsr(ResponseInterface $psrResponse): SfResponse
    {
        return (new HttpFoundationFactory())->createResponse($psrResponse);
    }
}