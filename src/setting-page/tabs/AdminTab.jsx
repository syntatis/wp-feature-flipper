import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import {
	AdminBarFieldset,
	DashboardWidgetsFieldset,
	SwitchFieldset,
} from '../fieldset';
import { HelpContent } from '../components';

const wpFooter = document.querySelector( '#wpfooter' );
const wpFooterDisplayStyle = wpFooter?.style?.display;

export const AdminTab = () => {
	const { getOption } = useSettingsContext();
	return (
		<Form>
			<Fieldset>
				<DashboardWidgetsFieldset />
				<SwitchFieldset
					name="admin_footer_text"
					id="admin-footer-text"
					title={ __( 'Footer Text', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show the footer text',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the footer text in the admin area will be shown.',
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
					<SwitchFieldset
						name="update_nags"
						id="update-nags"
						help={
							<HelpContent>
								<p>
									{ __(
										'This option will only remove the update notice that appear at the top of the admin area.It does not prevent the updates itself. To disable the updates, you can switch them off from the "Advanced › Updates" option.',
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
							'Enable update notification message',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'If switched off, notification message will not be shown when update is available.',
							'syntatis-feature-flipper'
						) }
					/>
				) }
			</Fieldset>
			<Fieldset
				title={ __( 'Admin Bar', 'syntatis-feature-flipper' ) }
				description={ __(
					'Options to customize the Admin Bar area',
					'syntatis-feature-flipper'
				) }
			>
				<AdminBarFieldset />
				<SwitchFieldset
					name="admin_bar_howdy"
					id="admin-bar-howdy"
					title={ __( 'Howdy Text', 'syntatis-feature-flipper' ) }
					label={ __(
						'Show the "Howdy" text',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the "Howdy" text in the Account menu in the admin bar will be removed.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchFieldset
					name="admin_bar_env_type"
					id="admin-bar-env-type"
					title={ __(
						'Environment Type',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Show the current environment type',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the current environment type of the site will not be shown in the Admin Bar.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'In WordPress, the "environment type" identifies the setup of a site at different stages. These types include:',
									'syntatis-feature-flipper'
								) }
							</p>
							<ul>
								<li>
									<strong>
										{ __(
											'Local:',
											'syntatis-feature-flipper'
										) }{ ' ' }
									</strong>
									{ __(
										'For development and testing on a local computer.',
										'syntatis-feature-flipper'
									) }
								</li>
								<li>
									<strong>
										{ __(
											'Development:',
											'syntatis-feature-flipper'
										) }{ ' ' }
									</strong>
									{ __(
										'For testing the site, usually on a remote server, before moving to staging.',
										'syntatis-feature-flipper'
									) }
								</li>
								<li>
									<strong>
										{ __(
											'Staging:',
											'syntatis-feature-flipper'
										) }{ ' ' }
									</strong>
									{ __(
										'A copy of the live site for safely testing changes.',
										'syntatis-feature-flipper'
									) }
								</li>
								<li>
									<strong>
										{ __(
											'Production:',
											'syntatis-feature-flipper'
										) }{ ' ' }
									</strong>
									{ __(
										'The live site that users visit, where stability and security are crucial.',
										'syntatis-feature-flipper'
									) }
								</li>
							</ul>
						</HelpContent>
					}
				/>
			</Fieldset>
		</Form>
	);
};
