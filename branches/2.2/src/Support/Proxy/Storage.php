<?php

declare(strict_types=1);

namespace tiFy\Support\Proxy;

use Pollen\Filesystem\FilesystemInterface;
use Pollen\Filesystem\LocalFilesystemAdapterInterface;
use Pollen\Filesystem\LocalFilesystemInterface;
use Pollen\Filesystem\LocalImageFilesystemInterface;
use Pollen\Filesystem\StorageManagerInterface;

/**
 * @method static StorageManagerInterface addDisk(string $name, FilesystemInterface $disk)
 * @method static StorageManagerInterface addLocalDisk(string $name, LocalFilesystemInterface $disk)
 * @method static LocalFilesystemAdapterInterface createLocalAdapter(string $root, array $config = [])
 * @method static FilesystemInterface|null disk(?string $name = null)
 * @method static FilesystemInterface|null getDefaultDisk()
 * @method static LocalFilesystemInterface registerLocalDisk(string $name, string $root, array $config = [])
 * @method static LocalImageFilesystemInterface  registerLocalImageDisk(string $name, string $root, array $config = [])
 * @method static StorageManagerInterface setDefaultDisk(FilesystemInterface $defaultDisk)
 */
class Storage extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return StorageManagerInterface
     */
    public static function getInstance(): StorageManagerInterface
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return StorageManagerInterface::class;
    }
}