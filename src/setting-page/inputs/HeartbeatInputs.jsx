import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Details, HelpContent } from '../components';
import { Checkbox, Option, Select } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './HeartbeatInputs.module.scss';
import { useState } from '@wordpress/element';

const OPTION_KEYS = [
	'heartbeat',
	'heartbeat_admin',
	'heartbeat_admin_interval',
	'heartbeat_post_editor',
	'heartbeat_post_editor_interval',
	'heartbeat_front',
	'heartbeat_front_interval',
];

export const HeartbeatInputs = () => {
	const { inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ values, setValues ] = useState(
		OPTION_KEYS.reduce( ( acc, key ) => {
			acc[ key ] = getOption( key );
			return acc;
		}, {} )
	);

	return (
		<SwitchInput
			name="heartbeat"
			id="heartbeat"
			title="Heartbeat"
			onChange={ ( checked ) => {
				setValues( ( currentValues ) => {
					return {
						...currentValues,
						heartbeat: checked,
					};
				} );
			} }
			label={ __(
				'Enable the Heartbeat API',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'When switched off, WordPress will will not be sending requests to the Heartbeat API.',
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
				<Details
					summary={ __( 'Settings', 'syntatis-feature-flipper' ) }
				>
					<div className={ styles.group }>
						<div
							id="heartbeat-on-front"
							role="group"
							aria-labelledby="heartbeat-on-front-head"
						>
							<div id="heartbeat-on-front-head">
								<strong>{ __( 'On front pages' ) }</strong>
							</div>
							<Checkbox
								{ ...inputProps( 'heartbeat_front' ) }
								defaultSelected={ values.heartbeat_front }
								onChange={ ( checked ) => {
									setValues( ( currentValues ) => {
										return {
											...currentValues,
											heartbeat_front: checked,
										};
									} );
									setFieldsetValues(
										'heartbeat_front',
										checked
									);
								} }
								label={ __(
									'Enable request on the front pages once every',
									'syntatis-feature-flipper'
								) }
								suffix={
									<Select
										{ ...inputProps(
											'heartbeat_front_interval'
										) }
										selectedItem={
											values.heartbeat_front_interval
										}
										isDisabled={ ! values.heartbeat_front }
										onSelectionChange={ ( value ) => {
											setFieldsetValues(
												'heartbeat_front_interval',
												value
											);
										} }
										name="heartbeat_front_interval"
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
							id="heartbeat-on-admin"
							role="group"
							aria-labelledby="heartbeat-on-admin-head"
						>
							<div id="heartbeat-on-admin-head">
								<strong>{ __( 'On admin pages' ) }</strong>
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
									setFieldsetValues(
										'heartbeat_admin',
										checked
									);
								} }
								label={ __(
									'Set request on the admin pages once every',
									'syntatis-feature-flipper'
								) }
								suffix={
									<Select
										{ ...inputProps(
											'heartbeat_admin_interval'
										) }
										name="heartbeat_admin"
										selectedItem={
											values.heartbeat_admin_interval
										}
										isDisabled={ ! values.heartbeat_admin }
										onSelectionChange={ ( value ) => {
											setFieldsetValues(
												'heartbeat_admin_interval',
												value
											);
										} }
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
							id="heartbeat-on-post-edit"
							role="group"
							aria-labelledby="heartbeat-on-post-edit-head"
						>
							<div id="heartbeat-on-post-edit-head">
								<strong>
									{ __( 'On post edit screens' ) }
								</strong>
							</div>
							<Select
								{ ...inputProps(
									'heartbeat_post_editor_interval'
								) }
								name="heartbeat_post_editor"
								selectedItem={
									values.heartbeat_post_editor_interval
								}
								isDisabled={ ! values.heartbeat_post_editor }
								onSelectionChange={ ( value ) => {
									setFieldsetValues(
										'heartbeat_post_editor_interval',
										value
									);
								} }
								prefix={
									<Checkbox
										{ ...inputProps(
											'heartbeat_post_editor'
										) }
										defaultSelected={
											values.heartbeat_post_editor
										}
										onChange={ ( checked ) => {
											setValues( ( currentValues ) => {
												return {
													...currentValues,
													heartbeat_post_editor:
														checked,
												};
											} );
											setFieldsetValues(
												'heartbeat_admin',
												checked
											);
										} }
										label={ __(
											'Enable request on the post edit screen once every',
											'syntatis-feature-flipper'
										) }
									/>
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
						</div>
					</div>
				</Details>
			) }
		</SwitchInput>
	);
};
