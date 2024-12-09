import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { AdminBarInputs, DashboardWidgetsInputs, SwitchInput } from '../inputs';

const wpFooter = document.querySelector( '#wpfooter' );
const wpFooterDisplayStyle = wpFooter?.style?.display;

export const AdminTab = () => {
	return (
		<Form>
			<Fieldset>
				<DashboardWidgetsInputs />
				<SwitchInput
					name="admin_footer_text"
					id="admin-footer-text"
					title={ __( 'Footer Text', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show the footer text',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, the footer text in the admin area will be removed.',
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
						'When switched off, WordPress will not show notification message when update is available.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
			<Fieldset
				title={ __( 'Admin Bar', 'syntatis-feature-flipper' ) }
				description={ __(
					'Customize the Admin bar area',
					'syntatis-feature-flipper'
				) }
			>
				<AdminBarInputs />
				<SwitchInput
					name="admin_bar_howdy"
					id="admin-bar-howdy"
					title={ __( 'Howdy Text', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show the "Howdy" text',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, the "Howdy" text in the Account menu in the admin bar will be removed.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
