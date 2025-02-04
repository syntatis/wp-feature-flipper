import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { RadioGroupFieldset, SwitchFieldset } from '../fieldset';
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
					title={ __( 'File Edit', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the File Editor',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, it will disable the WordPress built-in file editor for themes and plugins.',
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
				<SwitchFieldset
					name="xmlrpc"
					id="xmlrpc"
					title={ __( 'XML-RPC', 'syntatis-feature-flipper' ) }
					label={ __( 'Enable XML-RPC', 'syntatis-feature-flipper' ) }
					description={ __(
						'If switched off, it will disable the XML-RPC endpoint.',
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
						'If switched off, it will allow users to make request to the public API endpoints without authentication.',
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
				<SwitchFieldset
					name="obfuscate_usernames"
					id="obfuscate-usernames"
					title={ __(
						'Obfuscate Usernames',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Obfuscate usernames with random identifier',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched on, it will obfuscate usernames of the users to hide the real ones.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									"By default, WordPress uses the username in the author's URL. This poses a security risk as it exposes author's username to login, making brute-force attacks easier.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"You can enable this option to obfuscate the user's real username/slug in the site public-facing URLs with a random identity to mitigate the risk."
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
			<Fieldset
				title={ __( 'Login', 'syntatis-feature-flipper' ) }
				description={ __(
					'Options to harden the login page on the site.',
					'syntatis-feature-flipper'
				) }
			>
				<RadioGroupFieldset
					name="login_identifier"
					id="login-identifier"
					title={ __( 'Identifier', 'syntatis-feature-flipper' ) }
					description={ __(
						'Select which user identifier to use for login.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'By default, WordPress allows users to log in using either their username or email address. This setting allows you to restrict the login to only one of these options.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"It's generally recommended to use only the email address to make it harder for attackers to guess the login credentials.",
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
					options={ [
						{
							label: __(
								'Both username and email',
								'syntatis-feature-flipper'
							),
							value: 'both',
						},
						{
							label: __(
								'Only username',
								'syntatis-feature-flipper'
							),
							value: 'username',
						},
						{
							label: __(
								'Only email',
								'syntatis-feature-flipper'
							),
							value: 'email',
						},
					] }
				/>
				<SwitchFieldset
					name="obfuscate_login_error"
					id="obfuscate-login-error"
					title={ __(
						'Obfuscate Error',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Obfuscate the error message when login fails',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched on, a more generic error message will be shown when the login fails.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'WordPress shows detailed error messages on the login page when someone types the wrong username or password. These messages are helpful for users but can also give hackers clues to break into your site.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'This setting, when enabled, will obfuscate the error message to make it less informative, which can help protect your site and makes it harder for hackers to guess the correct username or password.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
				<SwitchFieldset
					name="login_block_bots"
					id="login-block-bots"
					title={ __( 'Block Bots', 'syntatis-feature-flipper' ) }
					label={ __(
						'Block bots from the login page',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched on, known bots, crawlers, and spiders will be blocked from accessing the login page.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'Your login page should only be accessed by humans, not bots or crawlers. Bots can try to brute-force their way into your site by guessing usernames and passwords.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'This setting, when enabled, will block known bots and crawlers from accessing the login page, which can help protect your site from brute-force attacks.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'Keep in mind that this setting is only intended to block common known bots. It may not block sophisticated bots or human attackers.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
			<Fieldset
				title={ __( 'Passwords', 'syntatis-feature-flipper' ) }
				description={ __(
					'Options to configure the passwords used on the site.',
					'syntatis-feature-flipper'
				) }
			>
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
