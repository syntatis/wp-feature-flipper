import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { TextArea, TextField } from '@syntatis/kubrick';
import { RadioGroupFieldset } from './RadioGroupFieldset';
import { Fieldset, useSettingsContext } from '../form';
import styles from './SiteAccessFieldset.module.scss';

export const SiteAccessFieldset = () => {
	const { getOption } = useSettingsContext();
	const [ siteAccess, setSiteAccess ] = useState(
		getOption( 'site_access' )
	);

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
				onChange={ setSiteAccess }
				options={ [
					{
						label: __( 'Public', 'syntatis-feature-flipper' ),
						value: 'public',
					},
					{
						label: __( 'Private', 'syntatis-feature-flipper' ),
						value: 'private',
					},
					{
						label: __( 'Maintenance', 'syntatis-feature-flipper' ),
						value: 'maintenance',
					},
				] }
			>
				{ siteAccess === 'maintenance' && (
					<div
						className={ styles.settings }
						role="group"
						aria-labelledby="site-access-maintenance"
					>
						<TextField
							className="regular-text"
							label={ __(
								'Headline',
								'syntatis-feature-flipper'
							) }
							defaultValue={
								getOption( 'site_maintenance_args' ).headline
							}
							description={ __(
								'The title to display on the maintenance page.',
								'syntatis-feature-flipper'
							) }
						/>
						<TextArea
							className="regular-text"
							label={ __(
								'Description',
								'syntatis-feature-flipper'
							) }
							defaultValue={
								getOption( 'site_maintenance_args' ).description
							}
							description={ __(
								'The message to display on the maintenance page.',
								'syntatis-feature-flipper'
							) }
						/>
					</div>
				) }
			</RadioGroupFieldset>
		</Fieldset>
	);
};
