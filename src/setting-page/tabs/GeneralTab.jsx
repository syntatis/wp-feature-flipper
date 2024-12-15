import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import { RevisionsInputs, SwitchInput, UpdatesInputs } from '../inputs';
import { Details, HelpContent } from '../components';

export const GeneralTab = () => {
	const { inlineData } = useSettingsContext();

	return (
		<Form>
			<Fieldset>
				<SwitchInput
					name="gutenberg"
					id="gutenberg"
					title="Gutenberg"
					label={ __(
						'Enable the block editor',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, the block editor will be disabled and the classic editor will be used.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					isSelected={
						inlineData.themeSupport.widgetsBlockEditor === true
							? undefined
							: false
					}
					isDisabled={
						inlineData.themeSupport.widgetsBlockEditor === true
							? undefined
							: true
					}
					name="block_based_widgets"
					id="block-based-widgets"
					title="Block-based Widgets"
					label={ __(
						'Enable the block-based widgets',
						'syntatis-feature-flipper'
					) }
					description={
						inlineData.themeSupport.widgetsBlockEditor
							? __(
									'When switched off, the block-based widgets will be disabled and the classic widgets will be used.',
									'syntatis-feature-flipper'
							  )
							: __(
									'The current theme in-use does not support block-based widgets.',
									'syntatis-feature-flipper'
							  )
					}
				/>
				<SwitchInput
					name="comments"
					id="comments"
					title="Comments"
					label={ __(
						'Enable comments',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, comments will be disabled site-wide.',
						'syntatis-feature-flipper'
					) }
				/>
				<RevisionsInputs />
				<SwitchInput
					name="embed"
					id="embed"
					title="Embed"
					label={ __(
						'Enable post embedding',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will disable other sites from embedding content from your site, and vice-versa.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="self_ping"
					id="self-ping"
					title="Self-ping"
					label={ __(
						'Enable self-pingbacks',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not send pingbacks to your own site.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent readmore="https://wordpress.org/documentation/article/trackbacks-and-pingbacks/#pingbacks">
							<p>
								{ __(
									'Pingbacks in WordPress are automatic notifications sent when one blog links to another. If a post links to a pingback-enabled post on another site, the linked site gets a notification that appears as a comment, encouraging interaction between bloggers.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'However, WordPress also sends pingbacks for internal links within the same site by default, which can result in unnecessary and sometimes annoying self-pings. Disabling self-pingbacks can help keep your comments section clean and relevant.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
				<SwitchInput
					name="feeds"
					id="feeds"
					title="Feeds"
					label={ __(
						'Enable RSS feeds',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, it will disable the RSS feed URLs.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'An RSS feed, which stands for "Really Simple Syndication" or "Rich Site Summary", is a standardized format that allows users to receive updates of your site content, usually through a feed reader such as Feedly, Inoreader or Reeder app.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'Disabling feeds can help prevent content scraping and reduce server load.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
			<Details summary={ __( 'Advanced', 'syntatis-feature-flipper' ) }>
				<Fieldset
					title={ __( 'Advanced', 'syntatis-feature-flipper' ) }
					description={ __(
						'These features require more caution or technical knowledge to manage.',
						'syntatis-feature-flipper'
					) }
				>
					<UpdatesInputs />
					<SwitchInput
						name="cron"
						id="cron"
						title="Cron"
						label={ __(
							'Enable cron',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'When switched off, WordPress will not run scheduled events.',
							'syntatis-feature-flipper'
						) }
						help={
							<HelpContent>
								<p>
									{ __(
										'WP-Cron is an integral scheduling system within WordPress that manages time-based tasks automatically.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'It allows WordPress to perform scheduled tasks like publishing scheduled posts, checking for plugin or theme updates, sending email notifications, and more.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'When disabling this feature, it is recommended to set up a server cron job to trigger WP-Cron.',
										'syntatis-feature-flipper'
									) }
								</p>
							</HelpContent>
						}
					/>
					<SwitchInput
						name="heartbeat"
						id="heartbeat"
						title="Heartbeat"
						label={ __(
							'Enable the Heartbeat API',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'When switched off, the Heartbeat API will be disabled; it will not be sending requests.',
							'syntatis-feature-flipper'
						) }
						help={
							<HelpContent>
								<p>
									{ __(
										'The Heartbeat API enables real-time communication between the browser and server using periodic AJAX requests.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'It powers features like auto-saving posts, post locking to prevent simultaneous edits, session management to extend login times, and real-time dashboard updates for plugins.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'While it improves interactivity and functionality, it can increase server load, especially on shared hosting. You may disable it if necessary for performance optimization.',
										'syntatis-feature-flipper'
									) }
								</p>
							</HelpContent>
						}
					/>
				</Fieldset>
			</Details>
		</Form>
	);
};
