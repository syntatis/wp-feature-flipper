import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { TextArea, TextField } from '@syntatis/kubrick';
import { RadioGroupFieldset } from './RadioGroupFieldset';
import { Fieldset, useSettingsContext } from '../form';
import styles from './SiteAccessFieldset.module.scss';
import { SwitchFieldset } from './SwitchFieldset';
import { Details } from '../components';

export const SiteAccessFieldset = () => {
	const { getOption, optionPrefix } = useSettingsContext();
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
					'Select how your site should be accessible to visitors.',
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
					<Details
						summary={ __( 'Settings', 'syntatis-feature-flipper' ) }
					>
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
								name={ `${ optionPrefix }site_maintenance_args[headline]` }
								defaultValue={
									getOption( 'site_maintenance_args' )
										.headline
								}
								description={ __(
									'Provide a headline to display on the maintenance page.',
									'syntatis-feature-flipper'
								) }
								isRequired
								validate={ ( value ) => {
									if ( ! value.trim() ) {
										return __(
											'Please provide a headline for the maintenance page.',
											'syntatis-feature-flipper'
										);
									}
								} }
								validationBehavior="native"
							/>
							<TextArea
								className="regular-text"
								label={ __(
									'Message',
									'syntatis-feature-flipper'
								) }
								name={ `${ optionPrefix }site_maintenance_args[message]` }
								defaultValue={
									getOption( 'site_maintenance_args' ).message
								}
								description={ __(
									'Provide a brief message to display on the maintenance page.',
									'syntatis-feature-flipper'
								) }
								isRequired
								validationBehavior="native"
								validate={ ( value ) => {
									if ( ! value.trim() ) {
										return __(
											'Please provide a brief message for the maintenance page.',
											'syntatis-feature-flipper'
										);
									}
								} }
							/>
						</div>
					</Details>
				) }
			</RadioGroupFieldset>
			<SwitchFieldset
				name="sitemap"
				id="sitemap"
				title={ __( 'Sitemap', 'syntatis-feature-flipper' ) }
				label={ __( 'Enable Sitemap', 'syntatis-feature-flipper' ) }
				description={ __(
					'If switched off, the wp-sitemap.xml file will not be generated.',
					'syntatis-feature-flipper'
				) }
				help={ __(
					'The Sitempa file helps search engines find and index your site content. WordPress, since version 5.5, automatically generates a Sitemap file at wp-sitemap.xml. If you would like to keep your site fully private or not indexed, you can disable this feature.',
					'syntatis-feature-flipper'
				) }
			/>
		</Fieldset>
	);
};
