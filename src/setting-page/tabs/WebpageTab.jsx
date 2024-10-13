import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

export const WebpageTab = () => {
	return (
		<Form>
			<Fieldset
				title="Head"
				description={ __(
					'Control the document metadata added in the HTML head.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchInput
					name="rsd_link"
					id="rsd-link"
					label={ __( 'RSD Link', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", it will remove the Really Simple Discovery (RSD) link from the webpage head.',
						'syntatis-feature-flipper'
					) }
				>
					{ __( 'Add RSD link', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="wordpress_generator"
					id="wordpress-generator"
					label={ __(
						'Generator Meta Tag',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", it will remove the generator meta tag which shows WordPress and its version.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Add WordPress generator meta tag',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
				<SwitchInput
					name="shortlink"
					id="shortlink"
					label={ __( 'Shortlink', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", it will remove the shortlink meta tag which shows the short URL of the webpage head.',
						'syntatis-feature-flipper'
					) }
				>
					{ __( 'Add Shortlink', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="rest_api_link"
					id="rest_api_link"
					label={ __( 'REST API Link', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", it will remove the REST API link from the webpage head.',
						'syntatis-feature-flipper'
					) }
				>
					{ __( 'Add REST API link', 'syntatis-feature-flipper' ) }
				</SwitchInput>
			</Fieldset>
		</Form>
	);
};
