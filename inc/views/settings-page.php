<?php
/**
 * Render the plugin settings page.
 *
 * Called when user navigates to the plugin settings page. It will render
 * only with these HTML. The settings form, inputs, buttons will be
 * rendered with React components.
 *
 * @see ./src/settings/Page.jsx
 */

declare(strict_types=1);

use SSFV\Codex\Facades\App;

?>
<div class="wrap">
	<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	<div
		id="<?php echo esc_attr(App::name()); ?>-settings"
		data-nonce="<?php echo esc_attr(wp_create_nonce(App::name() . '-settings')); ?>"
		data-inline='<?php echo esc_attr($args['inline_data']); ?>'>
	</div>
	<noscript>
		<p>
			<?php esc_html_e('This setting page requires JavaScript to be enabled in your browser. Please enable JavaScript and reload the page.', 'syntatis-feature-flipper'); ?>
		</p>
	</noscript>
</div>
