<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem as LeagueFilesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Filesystem\Filesystem as FilesystemContract;
use tiFy\Support\Str;

class Filesystem extends LeagueFilesystem implements FilesystemContract
{
    /**
     * @inheritDoc
     */
    public function download(string $path, ?string $name = null, array $headers = []): StreamedResponse
    {
        return $this->response($path, $name, $headers, 'attachment');
    }

    /**
     * @inheritDoc
     */
    public function getRealAdapter(): AdapterInterface
    {
        $disk = $this->getAdapter();

        return $disk instanceof CachedAdapter ? $disk->getAdapter() : $disk;
    }

    /**
     * @inheritDoc
     */
    public function path($path): ?string
    {
        $adapter = $this->getRealAdapter();

        return $adapter instanceof Local ? $adapter->applyPathPrefix($path) : null;
    }

    /**
     * @inheritDoc
     */
    public function response(
        string $path,
        ?string $name = null,
        array $headers = [],
        $disposition = 'inline'
    ): StreamedResponse {
        $response = new StreamedResponse();
        $name = $name ?? basename($path);
        $name = Str::ascii($name);

        $disposition = $response->headers->makeDisposition($disposition, $name ?? basename($path));
        $response->headers->replace($headers + [
                'Content-Type'        => $this->getMimeType($path),
                'Content-Length'      => $this->getSize($path),
                'Content-Disposition' => $disposition,
            ]);
        $response->setCallback(function () use ($path) {
            $stream = $this->readStream($path);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }
}