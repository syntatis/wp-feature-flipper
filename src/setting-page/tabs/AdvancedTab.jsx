import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { SwitchInput, UpdatesInputs } from '../inputs';
import { HelpContent } from '../components';
import { Notice } from '@syntatis/kubrick';
import styles from './AdvancedTab.module.scss';

export const AdvancedTab = () => {
	return (
		<>
			<Notice className={ styles.notice } level="warning">
				<strong>
					{ __( 'Caution:', 'syntatis-feature-flipper' ) }
				</strong>
				<span>
					{ __(
						'Please be cautious when changing these settings as they can affect the functionality of the site.',
						'syntatis-feature-flipper'
					) }
				</span>
			</Notice>
			<Form>
				<Fieldset>
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
			</Form>
		</>
	);
};
