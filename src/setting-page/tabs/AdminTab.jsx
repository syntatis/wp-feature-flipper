import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import { AdminBarInputs, DashboardWidgetsInputs, SwitchInput } from '../inputs';
import { HelpContent } from '../components';

const wpFooter = document.querySelector( '#wpfooter' );
const wpFooterDisplayStyle = wpFooter?.style?.display;

export const AdminTab = () => {
	const { getOption } = useSettingsContext();
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
				{ getOption( 'updates' ) && (
					<SwitchInput
						name="update_nags"
						id="update-nags"
						help={
							<HelpContent>
								<p>
									{ __(
										'This option will only remove the update notice that appear at the top of the admin area.It does not prevent the updates itself. To disable the updates, you can switch them off from the "General › Advanced › Updates" option.',
										'syntatis-feature-flipper'
									) }
								</p>
							</HelpContent>
						}
						title={ __(
							'Update Nags',
							'syntatis-feature-flipper'
						) }
						label={ __(
							'Enable WordPress update notification message',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'When switched off, WordPress will not show notification message when update is available.',
							'syntatis-feature-flipper'
						) }
					/>
				) }
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
