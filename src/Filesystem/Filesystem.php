<?php declare(strict_types=1);

namespace tiFy\Filesystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Symfony\Component\HttpFoundation\StreamedResponse;
use tiFy\Contracts\Filesystem\Filesystem as FilesystemContract;

class Filesystem extends LeagueFilesystem implements FilesystemContract
{
    /**
     * @inheritdoc
     */
    public function download(string $path, ?string $name = null, array $headers = []): StreamedResponse
    {
        return $this->response($path, $name, $headers, 'attachment');
    }

    /**
     * @inheritdoc
     */
    public function response(
        string $path,
        ?string $name = null,
        array $headers = [],
        $disposition = 'inline'
    ): StreamedResponse {
        $response    = new StreamedResponse();
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

    /**
     * @inheritdoc
     */
    public function path($path): string
    {
        return $this->getAdapter()->applyPathPrefix($path);
    }
}