import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { SwitchInput } from '../inputs';
import { HelpContent } from '../components';

export const SiteTab = () => {
	return (
		<Form>
			<Fieldset
				title="Metadata"
				description={ __(
					'Control the document metadata added in the HTML head.',
					'syntatis-feature-flipper'
				) }
			>
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
