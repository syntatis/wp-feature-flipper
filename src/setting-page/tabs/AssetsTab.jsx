import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

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
						'Enable WordPress built-in emojis',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", WordPress will not load the emoji scripts, styles, and images.',
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
						'When set to "off", WordPress will not append the version number to the enqueued scripts and styles.',
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
						'When set to "off", WordPress will not load the jQuery Migrate script.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
