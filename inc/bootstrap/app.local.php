<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

use function class_exists;

if (! class_exists(Run::class)) {
	return;
}

$whoops = new Run();
$whoops->pushHandler(new PrettyPageHandler());
$whoops->register();
