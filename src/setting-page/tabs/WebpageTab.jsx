import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { SwitchInput } from '../inputs';

export const WebpageTab = () => {
	return (
		<Form>
			<Fieldset
				title="Metadata"
				description={ __(
					'Control the document metadata added in the HTML head.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchInput
					name="rsd_link"
					id="rsd-link"
					title={ __( 'RSD Link', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable RSD link',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will remove the Really Simple Discovery (RSD) link from the webpage head.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="generator_tag"
					id="generator-tag"
					title={ __(
						'Generator Meta Tag',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Add WordPress generator meta tag',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will remove the generator meta tag which shows WordPress and its version.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="shortlink"
					id="shortlink"
					title={ __( 'Shortlink', 'syntatis-feature-flipper' ) }
					label={ __( 'Add Shortlink', 'syntatis-feature-flipper' ) }
					description={ __(
						'When switched off, it will remove the shortlink meta tag which shows the short URL of the webpage head.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
