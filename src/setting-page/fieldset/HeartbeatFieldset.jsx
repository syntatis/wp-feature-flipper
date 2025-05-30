import { __ } from '@wordpress/i18n';
import { Checkbox, Option, Select } from '@syntatis/kubrick';
import { useState } from '@wordpress/element';
import { SwitchFieldset } from './SwitchFieldset';
import { HelpContent } from '../components';
import { useSettingsContext } from '../form';
import styles from './HeartbeatFieldset.module.scss';

const OPTION_KEYS = [
	'heartbeat',
	'heartbeat_admin',
	'heartbeat_admin_interval',
	'heartbeat_post_editor',
	'heartbeat_post_editor_interval',
];

export const HeartbeatFieldset = () => {
	const { inputProps, getOption, optionPrefix } = useSettingsContext();
	const [ values, setValues ] = useState(
		OPTION_KEYS.reduce( ( acc, key ) => {
			acc[ key ] = getOption( key );
			return acc;
		}, {} )
	);

	return (
		<SwitchFieldset
			name="heartbeat"
			id="heartbeat"
			title="Heartbeat"
			onChange={ ( checked ) => {
				setValues( ( currentValues ) => {
					OPTION_KEYS.forEach( ( key ) => {
						if ( ! key.endsWith( '_interval' ) ) {
							currentValues[ key ] = checked;
						}
					} );

					return currentValues;
				} );
			} }
			label={ __(
				'Enable the Heartbeat API',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'If switched off, WordPress will stop sending Heartbeat API requests.',
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
							'While it improves interactivity and functionality, it can increase server load, especially on shared hosting. You may customize the frequency if necessary for performance optimization.',
							'syntatis-feature-flipper'
						) }
					</p>
				</HelpContent>
			}
		>
			{ values.heartbeat && (
				<div className={ styles.group }>
					<div
						id="heartbeat-on-admin"
						role="group"
						aria-labelledby="heartbeat-on-admin-head"
					>
						<div id="heartbeat-on-admin-head">
							<strong>
								{ __( 'On admin', 'syntatis-feature-flipper' ) }
							</strong>
						</div>
						<Checkbox
							{ ...inputProps( 'heartbeat_admin' ) }
							defaultSelected={ values.heartbeat_admin }
							onChange={ ( checked ) => {
								setValues( ( currentValues ) => {
									return {
										...currentValues,
										heartbeat_admin: checked,
									};
								} );
							} }
							label={ __(
								'Enable request on the admin area once every',
								'syntatis-feature-flipper'
							) }
							suffix={
								<Select
									{ ...inputProps(
										'heartbeat_admin_interval'
									) }
									name={ undefined } // Name is set in the hidden input.
									onSelectionChange={ ( value ) => {
										setValues( ( currentValues ) => {
											return {
												...currentValues,
												heartbeat_admin_interval: value,
											};
										} );
									} }
									selectedItem={
										values.heartbeat_admin_interval
									}
									isDisabled={ ! values.heartbeat_admin }
								>
									<Option value={ 15 }>
										{ __(
											'15 seconds',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 30 }>
										{ __(
											'30 seconds',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 60 }>
										{ __(
											'1 minute',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 120 }>
										{ __(
											'2 minutes',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 300 }>
										{ __(
											'5 minutes',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 600 }>
										{ __(
											'10 minutes',
											'syntatis-feature-flipper'
										) }
									</Option>
								</Select>
							}
						/>
					</div>
					<div
						id="heartbeat-on-post-editor"
						role="group"
						aria-labelledby="heartbeat-on-post-editor-head"
					>
						<div id="heartbeat-on-post-editor-head">
							<strong>
								{ __(
									'On post editor',
									'syntatis-feature-flipper'
								) }
							</strong>
						</div>
						<Checkbox
							{ ...inputProps( 'heartbeat_post_editor' ) }
							defaultSelected={ values.heartbeat_post_editor }
							onChange={ ( checked ) => {
								setValues( ( currentValues ) => {
									return {
										...currentValues,
										heartbeat_post_editor: checked,
									};
								} );
							} }
							label={ __(
								'Enable request on the post editor once every',
								'syntatis-feature-flipper'
							) }
							suffix={
								<Select
									{ ...inputProps(
										'heartbeat_post_editor_interval'
									) }
									name={ undefined } // Name is set in the hidden input.
									onSelectionChange={ ( value ) => {
										setValues( ( currentValues ) => {
											return {
												...currentValues,
												heartbeat_post_editor_interval:
													value,
											};
										} );
									} }
									selectedItem={
										values.heartbeat_post_editor_interval
									}
									isDisabled={
										! values.heartbeat_post_editor
									}
								>
									<Option value={ 15 }>
										{ __(
											'15 seconds',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 30 }>
										{ __(
											'30 seconds',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 60 }>
										{ __(
											'1 minute',
											'syntatis-feature-flipper'
										) }
									</Option>
									<Option value={ 120 }>
										{ __(
											'2 minutes',
											'syntatis-feature-flipper'
										) }
									</Option>
								</Select>
							}
						/>
					</div>
					<input
						type="hidden"
						name={ `${ optionPrefix }heartbeat_admin_interval` }
						value={ values.heartbeat_admin_interval }
					/>
					<input
						type="hidden"
						name={ `${ optionPrefix }heartbeat_post_editor_interval` }
						value={ values.heartbeat_post_editor_interval }
					/>
				</div>
			) }
		</SwitchFieldset>
	);
};
