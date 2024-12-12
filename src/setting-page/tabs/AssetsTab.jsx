import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { SwitchInput } from '../inputs';

export const AssetsTab = () => {
	return (
		<Form>
			<Fieldset
				description={ __(
					'Control the behavior of scripts, styles, and images loaded on your site.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchInput
					name="emojis"
					id="emojis"
					title={ __( 'Emojis', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the WordPress built-in emojis',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not load the emojis scripts, styles, and images.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						"While Emojis are fun, if you're not using it in your posts or pages, you can safely disable them to reduce the number of requests from the additional scripts and styles require to load them.",
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="scripts_version"
					id="scripts-version"
					title={ __(
						'Scripts Version',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Show scripts and styles version',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not append the version to the scripts and styles URLs.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						'The version script specifies the version of a enqueued script in the URL query string like "?ver=5.9.3". This helps browsers detect updates and load the latest script instead of using an outdated cached one. Keeping this parameter enabled is recommended to ensure scripts are update correctly.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="jquery_migrate"
					id="scripts-version"
					title={ __( 'jQuery Migrate', 'syntatis-feature-flipper' ) }
					label={ __(
						'Load jQuery Migrate script',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not load the jQuery Migrate script.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						'jQuery Migrate is a WordPress library that ensures older themes and plugins work with newer versions of jQuery. This helps developers update their code while keeping older sites functional. If your site is up-to-date, you can safely disable jQuery Migrate.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
