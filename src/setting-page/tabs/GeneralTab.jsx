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
					label="Gutenberg"
					description={ __(
						'When set to "off", the block editor will be disabled for all the post types.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable the block editor',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
				<SwitchInput
					name="heartbeat"
					id="heartbeat"
					label="Heartbeat"
					description={ __(
						'When set to "off", the Heartbeat API will not send any requests.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable the Heartbeat API',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
				<SwitchInput
					name="self_ping"
					id="self-ping"
					label="Self-ping"
					description={ __(
						'When set to "off", WordPress will stop sending pings from your own site to your own site.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable self-pingbacks',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
				<SwitchInput
					name="cron"
					id="cron"
					label="Cron"
					description={ __(
						'When set to "off", WordPress will not run scheduled events.',
						'syntatis-feature-flipper'
					) }
				>
					{ __( 'Enable cron', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="embed"
					id="embed"
					label="Embed"
					description={ __(
						'When set to "off", it will disable other sites from embedding content from your site, and vice-versa.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable post embedding',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
				<SwitchInput
					name="feeds"
					id="feeds"
					label="Feeds"
					description={ __(
						'When set to "off", it will disable the RSS feed URLs.',
						'syntatis-feature-flipper'
					) }
				>
					{ __( 'Enable RSS feeds', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="auto_update"
					id="auto-update"
					label={ __( 'Auto Update', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", you will need to manually update WordPress.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable WordPress auto update',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
			</Fieldset>
		</Form>
	);
};
