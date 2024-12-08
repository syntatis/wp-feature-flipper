<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use NunoMaduro\Collision\Provider as Collision;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

use function class_exists;

if (! class_exists(Run::class) || ! class_exists(Collision::class)) {
	return;
}

if (Misc::isCommandLine()) {
	(new Collision())->register();
} else {
	$whoops = new Run();
	$whoops->pushHandler(new PrettyPageHandler());
	$whoops->register();
}
