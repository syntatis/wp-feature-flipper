import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

export const GeneralTab = () => {
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
						'When set to "off", WordPress will stop sending pings from your own site to your own site.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="cron"
					id="cron"
					title="Cron"
					label={ __( 'Enable cron', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", WordPress will not run scheduled events.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="embed"
					id="embed"
					title="Embed"
					label={ __(
						'Enable post embedding',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", it will disable other sites from embedding content from your site, and vice-versa.',
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
						'When set to "off", it will disable the RSS feed URLs.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="auto_update"
					id="auto-update"
					title={ __( 'Auto Update', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable WordPress auto update',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", you will need to manually update WordPress.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
