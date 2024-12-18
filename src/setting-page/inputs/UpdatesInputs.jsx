import { __ } from '@wordpress/i18n';
import { Checkbox, Switch } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './UpdatesInputs.module.scss';
import { useState } from '@wordpress/element';
import { Details } from '../components';

const OPTION_KEYS = [
	'auto_updates',
	'auto_update_core',
	'auto_update_plugins',
	'auto_update_themes',
	'updates',
	'update_core',
	'update_plugins',
	'update_themes',
];

export const UpdatesInputs = () => {
	const { inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ values, setValues ] = useState(
		OPTION_KEYS.reduce( ( acc, key ) => {
			acc[ key ] = getOption( key );
			return acc;
		}, {} )
	);

	function changeValues( keys, value ) {
		setValues( ( currentValues ) => {
			const newValues = { ...currentValues };
			keys.forEach( ( key ) => {
				newValues[ key ] = value;
			} );
			return newValues;
		} );
		keys.forEach( ( key ) => {
			setFieldsetValues( key, value );
		} );
	}

	return (
		<tr>
			<th scope="row">
				<span>{ __( 'Updates', 'syntatis-feature-flipper' ) }</span>
			</th>
			<td>
				<div className={ styles.inputGroup }>
					<div className={ styles.inputGroup }>
						<Switch
							{ ...inputProps( 'updates' ) }
							isSelected={ values.updates }
							onChange={ ( checked ) => {
								changeValues( OPTION_KEYS, checked );
							} }
							label={ __(
								'Enable all updates',
								'syntatis-feature-flipper'
							) }
							description={ __(
								'Enable updates for WordPress core, plugins, themes, and translations.',
								'syntatis-feature-flipper'
							) }
						/>
						{ values.updates && (
							<Switch
								{ ...inputProps( 'auto_updates' ) }
								onChange={ ( checked ) => {
									changeValues(
										OPTION_KEYS.filter( ( key ) => {
											return key.startsWith(
												'auto_update'
											);
										} ),
										checked
									);
								} }
								isSelected={ values.auto_updates }
								isReadOnly={ ! values.updates }
								defaultSelected={ values.auto_updates }
								description={ __(
									'Enable automatic updates for WordPress core, plugins, themes, and translations.',
									'syntatis-feature-flipper'
								) }
								label={ __(
									'Enable all automatic updates',
									'syntatis-feature-flipper'
								) }
							/>
						) }
					</div>
					{ values.updates && (
						<Details
							className={ styles.details }
							summary={ __(
								'Settings',
								'syntatis-feature-flipper'
							) }
						>
							<div className={ styles.inputGroup }>
								<div className={ styles.checkboxGroup }>
									<div
										aria-hidden
										className={ styles.heading }
									>
										{ __(
											'WordPress',
											'syntatis-feature-flipper'
										) }
									</div>
									<div className={ styles.inputs }>
										<Checkbox
											{ ...inputProps( 'update_core' ) }
											defaultSelected={
												values.update_core
											}
											onChange={ ( checked ) => {
												changeValues(
													[
														'update_core',
														'auto_update_core',
													],
													checked
												);
											} }
											isReadOnly={ ! values.updates }
											isSelected={
												values.updates &&
												values.update_core
											}
											aria-label={ __(
												'Enable WordPress core update',
												'syntatis-feature-flipper'
											) }
											label={ __(
												'Enable update',
												'syntatis-feature-flipper'
											) }
										/>
										{ values.update_core && (
											<Checkbox
												{ ...inputProps(
													'auto_update_core'
												) }
												onChange={ ( checked ) => {
													changeValues(
														[ 'auto_update_core' ],
														checked
													);
												} }
												isDisabled={
													! values.auto_updates
												}
												isSelected={
													values.auto_update_core &&
													values.update_core
												}
												aria-label={ __(
													'Enable WordPress core automatic update',
													'syntatis-feature-flipper'
												) }
												label={ __(
													'Enable automatic update',
													'syntatis-feature-flipper'
												) }
											/>
										) }
									</div>
									<p className="description" aria-hidden>
										{ __(
											'Manage WordPress core update configurations.',
											'syntatis-feature-flipper'
										) }
									</p>
								</div>
								<div className={ styles.checkboxGroup }>
									<div
										aria-hidden
										className={ styles.heading }
									>
										{ __(
											'Plugins',
											'syntatis-feature-flipper'
										) }
									</div>
									<div className={ styles.inputs }>
										<Checkbox
											{ ...inputProps(
												'update_plugins'
											) }
											aria-label={ __(
												'Enable plugins update',
												'syntatis-feature-flipper'
											) }
											label={ __(
												'Enable update',
												'syntatis-feature-flipper'
											) }
											isReadOnly={ ! values.updates }
											isSelected={
												values.update_plugins &&
												values.updates
											}
											onChange={ ( checked ) => {
												changeValues(
													[
														'auto_update_plugins',
														'update_plugins',
													],
													checked
												);
											} }
										/>
										{ values.update_plugins && (
											<Checkbox
												{ ...inputProps(
													'auto_update_plugins'
												) }
												isDisabled={
													! values.auto_updates
												}
												isSelected={
													values.auto_update_plugins &&
													values.update_plugins
												}
												label={ __(
													'Enable automatic update',
													'syntatis-feature-flipper'
												) }
												onChange={ ( checked ) => {
													changeValues(
														[
															'auto_update_plugins',
														],
														checked
													);
												} }
											/>
										) }
									</div>
									<p className="description">
										{ __(
											'Manage plugins update configurations.',
											'syntatis-feature-flipper'
										) }
									</p>
								</div>
								<div className={ styles.checkboxGroup }>
									<div
										aria-hidden
										className={ styles.heading }
									>
										{ __(
											'Themes',
											'syntatis-feature-flipper'
										) }
									</div>
									<div className={ styles.inputs }>
										<Checkbox
											{ ...inputProps( 'update_themes' ) }
											isReadOnly={ ! values.updates }
											isSelected={
												values.update_themes &&
												values.updates
											}
											aria-label={ __(
												'Enable themes update',
												'syntatis-feature-flipper'
											) }
											label={ __(
												'Enable update',
												'syntatis-feature-flipper'
											) }
											onChange={ ( checked ) => {
												changeValues(
													[
														'auto_update_themes',
														'update_themes',
													],
													checked
												);
											} }
										/>
										{ values.update_themes && (
											<Checkbox
												{ ...inputProps(
													'auto_update_themes'
												) }
												isDisabled={
													! values.auto_updates
												}
												isSelected={
													values.auto_update_themes &&
													values.update_themes
												}
												aria-label={ __(
													'Enable themes automatic update',
													'syntatis-feature-flipper'
												) }
												label={ __(
													'Enable automatic update',
													'syntatis-feature-flipper'
												) }
												onChange={ ( checked ) => {
													changeValues(
														[
															'auto_update_themes',
														],
														checked
													);
												} }
											/>
										) }
									</div>
									<p className="description">
										{ __(
											'Manage themes update configurations.',
											'syntatis-feature-flipper'
										) }
									</p>
								</div>
							</div>
						</Details>
					) }
				</div>
			</td>
		</tr>
	);
};
