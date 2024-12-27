import { __ } from '@wordpress/i18n';
import { Fieldset, useSettingsContext } from '../form';
import { RadioGroupFieldset } from './RadioGroupFieldset';
import { Radio } from '@syntatis/kubrick';

export const SiteAccessFieldset = () => {
	const { getOption } = useSettingsContext();

	return (
		<Fieldset>
			<RadioGroupFieldset
				name="site_access"
				id="site-access"
				title={ __( 'Access', 'syntatis-feature-flipper' ) }
				description={ __(
					'Select how accessible your site should be to visitors.',
					'syntatis-feature-flipper'
				) }
			>
				<Radio value="public">
					{ __( 'Public', 'syntais-feature-flipper' ) }
				</Radio>
				<Radio value="private">
					{ __( 'Private', 'syntais-feature-flipper' ) }
				</Radio>
				<Radio value="maintenance">
					{ __( 'Maintenance', 'syntais-feature-flipper' ) }
				</Radio>
			</RadioGroupFieldset>
		</Fieldset>
	);
};
