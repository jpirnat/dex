<?php
declare(strict_types=1);

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

$isDebug = $_SERVER['ENVIRONMENT'] !== 'production';

$file = __DIR__ . '/cache/container.php';
$containerConfigCache = new ConfigCache($file, $isDebug);

if (!$containerConfigCache->isFresh()) {
    $containerBuilder = new ContainerBuilder();
	$loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
	$loader->load('dependencies.php');
    $containerBuilder->compile();

    $dumper = new PhpDumper($containerBuilder);
    $containerConfigCache->write(
        $dumper->dump(['class' => 'MyCachedContainer']),
        $containerBuilder->getResources()
    );
}

require_once $file;
$container = new MyCachedContainer();

return $container;
