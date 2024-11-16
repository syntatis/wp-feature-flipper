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
					name="dashboard_widgets"
					id="dashboard-widgets"
					title={ __(
						'Dashboard Widgets',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable Dashboard widgets',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'Customize the widgets to show or hide on the WordPress dashboard.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="admin_footer_text"
					id="admin-footer-text"
					title={ __( 'Footer Text', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show the footer text',
						'syntatis-feature-flipper'
					) }
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
				/>
				<SwitchInput
					name="update_nags"
					id="update-nags"
					title={ __( 'Update Nags', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable WordPress update notification message',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", WordPress will not be showing notification message on the admin when update is available.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
			<Fieldset
				title={ __( 'Admin bar', 'syntatis-feature-flipper' ) }
				description={ __(
					'Customize the WordPress admin bar.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchInput
					name="admin_wordpress_logo"
					id="admin-wordpress-logo"
					title={ __( 'WordPress Logo', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show WordPress logo in the admin bar',
						'syntatis-feature-flipper'
					) }
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
				/>
				<SwitchInput
					name="account_menu_howdy"
					id="account-menu-howdy"
					title={ __( 'Show "Howdy"', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show "Howdy" in the admin bar',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", it will hide the "Howdy" from the account menu in the admin bar.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
