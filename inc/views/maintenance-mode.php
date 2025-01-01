<?php

declare(strict_types=1);

use Syntatis\FeatureFlipper\Helpers\Option;

$args = Option::get('site_maintenance_args');

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
	<style>
	#syntatis-feature-flipper-maintenance {
		display: flex;
		width: 100vw;
		height: 100vh;
		padding-inline: 20vw;
		box-sizing: border-box;
		text-align: center;
		justify-content: center;
		align-items: center;
		flex-flow: column wrap;
	}
	</style>
</head>
<body>
	<div id="syntatis-feature-flipper-maintenance">
		<h1><?php echo esc_html($args['headline'] ?? ''); ?></h1>
		<?php echo wp_kses_post(wpautop($args['description'] ?? '')); ?>
	</div>
	<?php wp_footer(); ?>
</body>
</html>
