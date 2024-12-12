import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import { RevisionsInputs, SwitchInput, UpdatesInputs } from '../inputs';

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
					help={ __(
						'Pingbacks in WordPress are automatic notifications sent when one blog links to another. If a post links to a pingback-enabled post on another site, the linked site gets a notification that appears as a comment, encouraging interaction between bloggers. However, WordPress also sends pingbacks for internal links within the same site by default, which can result in unnecessary and sometimes annoying self-pings. Disabling self-pingbacks can help keep your comments section clean and relevant.',
						'syntatis-feature-flipper'
					) }
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
					help={ __(
						'An RSS feed, which stands for "Really Simple Syndication" or "Rich Site Summary", is a web feed format that allows users to access updates from various websites in a standardized, computer-readable format. It allows them to receive notifications about new content and read it without needing to visit each website individually.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
			<details>
				<summary>
					{ __( 'Advanced', 'syntatis-feature-flipper' ) }
				</summary>
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
						help={ __(
							'WP-Cron is an integral scheduling system within WordPress that manages time-based tasks automatically. It allows WordPress to perform scheduled tasks like publishing scheduled posts, checking for plugin or theme updates, sending email notifications, and more. When disabling this feature, It is recommended to set up a server cron job to trigger WP-Cron.',
							'syntatis-feature-flipper'
						) }
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
						help={ __(
							'The Heartbeat API enables real-time communication between the browser and server using periodic AJAX requests. It powers features like auto-saving posts, post locking to prevent simultaneous edits, session management to extend login times, and real-time dashboard updates for plugins. While it enhances interactivity and functionality, it can increase server load, especially on shared hosting. You may disable it if necessary for performance optimization.',
							'syntatis-feature-flipper'
						) }
					/>
				</Fieldset>
			</details>
		</Form>
	);
};
