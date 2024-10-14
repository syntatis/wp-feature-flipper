import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

const wpAdminLogo = document.querySelector( '#wp-admin-bar-wp-logo' );
const wpAdminLogoDisplayStyle = wpAdminLogo?.style?.display;
const wpFooter = document.querySelector( '#wpfooter' );
const wpFooterDisplayStyle = wpFooter?.style?.display;

export const AdminTab = () => {
	return (
		<Form>
			<Fieldset>
				<SwitchInput
					name="admin_wordpress_logo"
					id="admin-wordpress-logo"
					label={ __( 'WordPress Logo', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", the WordPress logo will be hidden from the admin bar.',
						'syntatis-feature-flipper'
					) }
					onChange={ ( checked ) => {
						if ( wpAdminLogo ) {
							wpAdminLogo.style.display = checked
								? wpAdminLogoDisplayStyle
								: 'none';
						}
					} }
				>
					{ __(
						'Show WordPress logo in the admin bar',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
				<SwitchInput
					name="admin_footer_text"
					id="admin-footer-text"
					label={ __( 'Footer Text', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", the default WordPress footer text will be removed from the admin.',
						'syntatis-feature-flipper'
					) }
					onChange={ ( checked ) => {
						if ( wpFooter ) {
							wpFooter.style.display = checked
								? wpFooterDisplayStyle
								: 'none';
						}
					} }
				>
					{ __( 'Show the footer text', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="update_nags"
					id="update-nags"
					label={ __( 'Update Nags', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", WordPress will not be showing notification message on the admin when update is available.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable WordPress update notification message',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
			</Fieldset>
		</Form>
	);
};
