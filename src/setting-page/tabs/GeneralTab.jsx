import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../components/form';
import { RevisionsInputs, SwitchInput } from '../components/inputs';

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
						'When switched off, it will disable other sites from embedding content from your site, and vice-versa.',
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
						'When switched off, you will need to manually update WordPress.',
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
				</Fieldset>
			</details>
		</Form>
	);
};
