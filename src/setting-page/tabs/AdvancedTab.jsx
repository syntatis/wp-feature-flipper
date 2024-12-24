import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { HeartbeatInputs, SwitchInput, UpdatesInputs } from '../inputs';
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
							'If switched off, WordPress will not run scheduled events.',
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
					<HeartbeatInputs />
				</Fieldset>
			</Form>
		</>
	);
};
