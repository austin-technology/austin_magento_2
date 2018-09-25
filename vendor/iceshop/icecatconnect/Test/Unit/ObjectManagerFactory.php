<?php

namespace ICEShop\ICECatConnect\Test\Unit;

use Magento\Framework\App\ObjectManagerFactory as AppObjectManagerFactory;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ObjectManagerFactory
 */
class ObjectManagerFactory
{
    /**
     * @return ObjectManagerInterface
     */
    public function create($args = [])
    {
        return $this->createObjectManagerFactory()->create($args);
    }
    /**
     * @return AppObjectManagerFactory
     */
    protected function createObjectManagerFactory()
    {
        $driverPool = $this->createDriverPool();
        $directoryList = $this->createDirectoryList();
        $configFilePool = $this->createConfigFilePool();
        return new AppObjectManagerFactory($directoryList, $driverPool, $configFilePool);
    }
    /**
     * @return DriverPool
     */
    protected function createDriverPool()
    {
        return new DriverPool([
            'file' => File::class
        ]);
    }
    /**
     * @return DirectoryList
     */
    protected function createDirectoryList()
    {
        return new DirectoryList(BP, [
            'base' => [
                'path' => BP
            ]
        ]);
    }
    /**
     * @return ConfigFilePool
     */
    protected function createConfigFilePool()
    {
        return new ConfigFilePool();
    }

}