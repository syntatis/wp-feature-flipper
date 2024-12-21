import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { SwitchInput } from '../inputs';
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
				<SwitchInput
					name="file_edit"
					id="file-edit"
					title={ __( 'File Edit', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the File Editor',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will disable the WordPress built-in file editor for themes and plugins.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									"By default, WordPress allows admins to edit theme and plugin file directly from the dashboard, but it's a security risk. Mistakes can break the site, and hackers who gain access can exploit it to compromise all your data.",
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
				<SwitchInput
					name="xmlrpc"
					id="xmlrpc"
					title={ __( 'XML-RPC', 'syntatis-feature-flipper' ) }
					label={ __( 'Enable XML-RPC', 'syntatis-feature-flipper' ) }
					description={ __(
						'When switched off, it will disable the XML-RPC endpoint.',
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
				<SwitchInput
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
						'When switched off, third-party applications will not be able to authenticate with the site using the Application Passwords.',
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
									'It allows users to generate unique passwords that enable third-party applications to authenticate with the site without needing to expose the users main password.',
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
				<SwitchInput
					name="authenticated_rest_api"
					id="authenticated-rest-api"
					title={ __(
						'REST API Authentication',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable REST API Authentication',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will allow users to make request to the public REST API endpoint without authentication.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent readmore="https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/">
							<p>
								{ __(
									'When enabled, you will need to pass authenticattion with WordPress Password Application to access the REST API endpoints.',
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
