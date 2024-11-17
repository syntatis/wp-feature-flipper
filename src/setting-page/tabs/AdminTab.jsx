import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import {
	AdminBarInputs,
	DashboardWidgetsInputs,
	SwitchInput,
} from '../components/inputs';

const wpFooter = document.querySelector( '#wpfooter' );
const wpFooterDisplayStyle = wpFooter?.style?.display;

export const AdminTab = () => {
	return (
		<Form>
			<Fieldset>
				<DashboardWidgetsInputs
					widgets={
						window.$syntatis.featureFlipper.dashboardWidgets || []
					}
				/>
				<AdminBarInputs
					menu={ window.$syntatis.featureFlipper.adminBarMenu || [] }
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
		</Form>
	);
};
