<?php

namespace yzh52521\Flysystem\Upyun\Plugins;

use League\Flysystem\Plugin\AbstractPlugin;

/**
 * Class FileUrl
 *
 * @package yzh52521\flysystem\Upyun\Plugins
 */
class FileUrl extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'getUrl';
    }

    public function handle($path = null)
    {
        return $this->filesystem->getAdapter()->getUrl($path);
    }
}
