<?php

namespace cronv\Task\Management;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class cronvTaskManagementBundle extends AbstractBundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container DI container
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Specify the new path for Twig templates
        $templatePath = $this->getFolder(['Resources', 'views']);

        $paths = [$templatePath];

        if (isset($container->getExtensionConfig('twig')[0]['paths'])) {
            $configPaths = $container->getExtensionConfig('twig')[0]['paths'];
            $paths = array_merge($configPaths, $paths);
        }

        // Specify a new path to templates
        $container->prependExtensionConfig('twig', [
            'paths' => $paths,
        ]);
    }

    /**
     * Gets the Bundle directory path.
     *
     * @param string $defaultFolder Folder default 'src'
     * @return string
     */
    public function getPath(string $defaultFolder = 'src'): string
    {
        return join('', [parent::getPath(), DIRECTORY_SEPARATOR, $defaultFolder]);
    }

    /**
     * Get path to folder
     *
     * Concatenates the path to the current folder
     *
     * @param array $dirs Folder names
     * @return string
     */
    public function getFolder(array $dirs): string
    {
        return join('', [
            $this->getPath(),
            DIRECTORY_SEPARATOR,
            ...array_map(fn($dir) => $dir . DIRECTORY_SEPARATOR, $dirs)
        ]);
    }
}
