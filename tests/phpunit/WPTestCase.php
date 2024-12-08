<?php

declare(strict_types=1);

namespace Syntatis\Tests;

use WP_UnitTestCase;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

abstract class WPTestCase extends WP_UnitTestCase
{
	use ExpectOutputHelper;
}
