import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { SwitchFieldset } from '../fieldset';
import { HelpContent } from '../components';

const themeEditors = document.querySelector(
	'#adminmenu a[href="theme-editor.php"]'
);
const pluginEditors = document.querySelector(
	'#adminmenu a[href="plugin-editor.php"]'
);
const originalDisplay = {
	themeEditors: themeEditors?.parentElement?.style?.display,
	pluginEditors: pluginEditors?.parentElement?.style?.display,
};

export const SecurityTab = () => {
	return (
		<Form>
			<Fieldset>
				<SwitchFieldset
					name="file_edit"
					id="file-edit"
					title={ __( 'File Editor', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the File Editor',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the built-in file editor for themes and plugins will be disabled.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									"By default, WordPress allows admins to edit theme and plugin file directly from the admin area, but it's a security risk. Mistakes can break the site, and hackers who gain access can exploit it to compromise all your data.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"It's generally best to disable this feature to keep your site safer.",
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
					onChange={ ( checked ) => {
						if ( themeEditors ) {
							themeEditors.parentElement.style.display = ! checked
								? 'none'
								: originalDisplay.themeEditors;
						}
						if ( pluginEditors ) {
							pluginEditors.parentElement.style.display =
								! checked
									? 'none'
									: originalDisplay.pluginEditors;
						}
					} }
				/>
				<SwitchFieldset
					name="xmlrpc"
					id="xmlrpc"
					title="XML-RPC"
					label={ __( 'Enable XML-RPC', 'syntatis-feature-flipper' ) }
					description={ __(
						'If switched off, the XML-RPC endpoint will not be accessible.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'XML-RPC is a communication protocol in WordPress that allows external applications to interact with it remotely. Originally designed to support mobile publishing and remote management, it enables operations like posting and managing content.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"However, due to security vulnerabilities, it's generally recommended to disable it in favor of the more secure WordPress REST API.",
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
				<SwitchFieldset
					name="authenticated_rest_api"
					id="authenticated-rest-api"
					title={ __(
						'API Authentication',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable REST API Authentication',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, all public API will be accessible without authentication.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent readmore="https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/">
							<p>
								{ __(
									'When enabled, you will need to pass authentication using the WordPress Password Application to access the REST API endpoints.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
			<Fieldset
				title={ __( 'Password', 'syntatis-feature-flipper' ) }
				description={ __(
					'Settings to manage password policy on your site.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchFieldset
					name="password_reset"
					id="password-reset"
					title={ __( 'Password Reset', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable Password Reset',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, users will not be able to reset their passwords.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchFieldset
					name="application_passwords"
					id="application-passwords"
					title={ __(
						'Application Passwords',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable the Application Passwords',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, third-party applications will not be able to use Application Passwords for authentication.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									"The Application Passwords in WordPress is a security feature that's first introduced in WordPress 5.6.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'It allows users to generate unique passwords that enable third-party applications to authenticate without needing to share the users main password.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"If you don't use any third-party applications that require this feature, it's generally safe to disable it.",
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
		</Form>
	);
};
