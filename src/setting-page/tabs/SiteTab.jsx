import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import { SiteAccessFieldset, SwitchFieldset } from '../fieldset';
import { HelpContent } from '../components';

export const SiteTab = () => {
	const { getOption } = useSettingsContext();

	return (
		<Form>
			<SiteAccessFieldset />
			<Fieldset
				title={ __( 'Assets', 'syntatis-feature-flipper' ) }
				description={ __(
					'Settings to control the scripts, styles, and images loaded on the site.',
					'syntatis-feature-flipper'
				) }
			>
				<SwitchFieldset
					name="emojis"
					id="emojis"
					title={ __( 'Emojis', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the built-in emojis',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, WordPress will not load the emojis scripts, styles, and images.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						"While Emojis are fun, if you're not using it in your posts or pages, you might want to consider disabling them to reduce the number of requests from the additional scripts and styles require to load them.",
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchFieldset
					name="scripts_version"
					id="scripts-version"
					title={ __(
						'Scripts Version',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Add scripts and styles version',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the scripts and styles version will not be added in the URLs.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						'WordPress adds the script version on the file URL, e.g. "?ver=5.9.3". This version helps browsers detect updates and load the latest script instead of using an outdated cached one. Keeping this parameter enabled is generally recommended to ensure scripts are update correctly.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchFieldset
					name="jquery_migrate"
					id="jquery-migrate"
					title="jQuery Migrate"
					label={ __(
						'Load jQuery Migrate script',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the jQuery Migrate script file will not be loaded.',
						'syntatis-feature-flipper'
					) }
					help={ __(
						'jQuery Migrate is a library that ensures older themes and plugins work with newer versions of jQuery. This helps developers update their code while keeping older sites functional. If your site is up-to-date, you can safely disable jQuery Migrate.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
			<Fieldset
				title="Metadata"
				description={ __(
					'Settings to control the metadata added in the HTML document head section of the site.',
					'syntatis-feature-flipper'
				) }
			>
				{ getOption( 'xmlrpc' ) && (
					<SwitchFieldset
						name="rsd_link"
						id="rsd-link"
						title={ __( 'RSD Link', 'syntatis-feature-flipper' ) }
						label={ __(
							'Enable RSD link',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'If switched off, the Really Simple Discovery (RSD) link will be removed from the webpage head.',
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
				<SwitchFieldset
					name="generator_tag"
					id="generator-tag"
					title={ __(
						'Generator Meta Tag',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Add the generator meta tag',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the generator meta tag on the webpage head which shows the current version of WordPress installed will be removed.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'The generator meta tag reveals the current WordPress version installed, which can pose security risks by exposing potential vulnerabilities of the version.',
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
				<SwitchFieldset
					name="shortlink"
					id="shortlink"
					title={ __( 'Shortlink', 'syntatis-feature-flipper' ) }
					label={ __( 'Add Shortlink', 'syntatis-feature-flipper' ) }
					description={ __(
						'If switched off, the shortlink meta tag which shows the short URL on the webpage head will be removed.',
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
