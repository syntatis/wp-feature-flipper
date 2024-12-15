import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import { SwitchInput } from '../inputs';
import { HelpContent } from '../components';

export const SiteTab = () => {
	const { getOption } = useSettingsContext();

	return (
		<Form>
			<Fieldset
				title={ __( 'Assets', 'syntatis-feature-flipper' ) }
				description={ __(
					'Control the behavior of scripts, styles, and images loaded on the site.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchInput
					name="emojis"
					id="emojis"
					title={ __( 'Emojis', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the WordPress built-in emojis',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not load the emojis scripts, styles, and images.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						"While Emojis are fun, if you're not using it in your posts or pages, you can safely disable them to reduce the number of requests from the additional scripts and styles require to load them.",
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="scripts_version"
					id="scripts-version"
					title={ __(
						'Scripts Version',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Show scripts and styles version',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not append the version to the scripts and styles URLs.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						'The version script specifies the version of a enqueued script in the URL query string like "?ver=5.9.3". This helps browsers detect updates and load the latest script instead of using an outdated cached one. Keeping this parameter enabled is recommended to ensure scripts are update correctly.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="jquery_migrate"
					id="jquery-migrate"
					title={ __( 'jQuery Migrate', 'syntatis-feature-flipper' ) }
					label={ __(
						'Load jQuery Migrate script',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not load the jQuery Migrate script.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						'jQuery Migrate is a WordPress library that ensures older themes and plugins work with newer versions of jQuery. This helps developers update their code while keeping older sites functional. If your site is up-to-date, you can safely disable jQuery Migrate.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
			<Fieldset
				title="Metadata"
				description={ __(
					'Control the document metadata added in the HTML head.',
					'syntatis-feature-flipper'
				) }
			>
				{ getOption( 'xmlrpc' ) && (
					<SwitchInput
						name="rsd_link"
						id="rsd-link"
						title={ __( 'RSD Link', 'syntatis-feature-flipper' ) }
						label={ __(
							'Enable RSD link',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'When switched off, it will remove the Really Simple Discovery (RSD) link from the webpage head.',
							'syntatis-feature-flipper'
						) }
						help={
							<HelpContent>
								<p>
									{ __(
										'The RSD link, or "Really Simple Discovery", helps external applications detect services like publishing APIs on your site. However, it\'s mostly outdated and rarely used today, so removing it from your site is usually safe.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'Note that this action only removes the link itself, not the underlying RSD endpoint. To fully disable the endpoint, you can turn off XML-RPC from "Security › XML-RPC".',
										'syntatis-feature-flipper'
									) }
								</p>
							</HelpContent>
						}
					/>
				) }
				<SwitchInput
					name="generator_tag"
					id="generator-tag"
					title={ __(
						'Generator Meta Tag',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Add WordPress generator meta tag',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will remove the generator meta tag which shows WordPress and its version.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'The generator meta tag in WordPress reveals the CMS version used on a site, which can pose security risks by exposing potential vulnerabilities to attackers.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'Removing this tag can help protect your site from such threats.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
				<SwitchInput
					name="shortlink"
					id="shortlink"
					title={ __( 'Shortlink', 'syntatis-feature-flipper' ) }
					label={ __( 'Add Shortlink', 'syntatis-feature-flipper' ) }
					description={ __(
						'When switched off, it will remove the shortlink meta tag which shows the short URL of the webpage head.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'Shortlink is a simplified URL created for easier sharing, but they are often unnecessary.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"Removing shortlink can clean up your site's HTML.",
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
