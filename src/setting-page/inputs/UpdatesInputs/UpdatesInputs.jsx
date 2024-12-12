import { __ } from '@wordpress/i18n';
import { Checkbox, Switch } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../../form';
import styles from './UpdatesInputs.module.scss';
import { useState } from '@wordpress/element';
import { Details } from '../../components';

const OPTION_KEYS = {
	autoUpdate: 'auto_updates',
	autoUpdateCore: 'auto_update_core',
	autoUpdatePlugins: 'auto_update_plugins',
	autoUpdateThemes: 'auto_update_themes',
	updates: 'updates',
	updateCore: 'update_core',
	updatePlugins: 'update_plugins',
	updateThemes: 'update_themes',
};

export const UpdatesInputs = () => {
	const { inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ values, setValues ] = useState(
		Object.keys( OPTION_KEYS ).reduce( ( acc, key ) => {
			acc[ key ] = getOption( OPTION_KEYS[ key ] );
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
			setFieldsetValues( OPTION_KEYS[ key ], value );
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
								changeValues(
									Object.keys( OPTION_KEYS ),
									checked
								);
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
										Object.keys( OPTION_KEYS ).filter(
											( key ) => {
												return key.startsWith(
													'autoUpdate'
												);
											}
										),
										checked
									);
								} }
								isSelected={ values.autoUpdate }
								isReadOnly={ ! values.updates }
								defaultSelected={ values.autoUpdate }
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
												values.updateCore
											}
											onChange={ ( checked ) => {
												changeValues(
													[
														'updateCore',
														'autoUpdateCore',
													],
													checked
												);
											} }
											isReadOnly={ ! values.updates }
											isSelected={
												values.updates &&
												values.updateCore
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
										{ values.updateCore && (
											<Checkbox
												{ ...inputProps(
													'auto_update_core'
												) }
												onChange={ ( checked ) => {
													changeValues(
														[ 'autoUpdateCore' ],
														checked
													);
												} }
												isDisabled={
													! values.autoUpdate
												}
												isSelected={
													values.autoUpdateCore &&
													values.updateCore
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
											'WordPress core updates configurations.',
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
												values.updatePlugins &&
												values.updates
											}
											onChange={ ( checked ) => {
												changeValues(
													[
														'autoUpdatePlugins',
														'updatePlugins',
													],
													checked
												);
											} }
										/>
										{ values.updatePlugins && (
											<Checkbox
												{ ...inputProps(
													'auto_update_plugins'
												) }
												isDisabled={
													! values.autoUpdate
												}
												isSelected={
													values.autoUpdatePlugins &&
													values.updatePlugins
												}
												label={ __(
													'Enable automatic update',
													'syntatis-feature-flipper'
												) }
												onChange={ ( checked ) => {
													changeValues(
														[ 'autoUpdatePlugins' ],
														checked
													);
												} }
											/>
										) }
									</div>
									<p className="description">
										{ __(
											'Plugins update configurations.',
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
												values.updateThemes &&
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
														'autoUpdateThemes',
														'updateThemes',
													],
													checked
												);
											} }
										/>
										{ values.updateThemes && (
											<Checkbox
												{ ...inputProps(
													'auto_update_themes'
												) }
												isDisabled={
													! values.autoUpdate
												}
												isSelected={
													values.autoUpdateThemes &&
													values.updateThemes
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
														[ 'autoUpdateThemes' ],
														checked
													);
												} }
											/>
										) }
									</div>
									<p className="description">
										{ __(
											'Themes update configurations.',
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
