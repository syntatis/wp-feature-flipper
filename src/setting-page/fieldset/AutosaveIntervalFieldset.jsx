/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Switch, TextField } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { HelpContent, HelpTip } from '../components';
import styles from './styles.module.scss';

export const AutosaveIntervalFieldset = ( { isDisabled = false } ) => {
	const { getOption, inputProps, labelProps, inlineData } =
		useSettingsContext();

	const autosave = inlineData.features?.autosaveInterval || {};
	const isLocked = autosave.isLocked === true;
	const defaultInterval = Number( autosave.defaultInterval );
	const minInterval = Number( autosave.minInterval );
	const maxInterval = Number( autosave.maxInterval );

	const option = getOption( 'autosave_interval' );
	const parsed = Number( option );
	const initialInterval =
		Number.isFinite( parsed ) && parsed > 0 ? parsed : defaultInterval;
	const [ interval, setInterval ] = useState( initialInterval );
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'autosave_interval_enabled' )
	);

	return (
		<tr>
			<th scope="row">
				<span className={ styles.label }>
					<label { ...labelProps( 'autosave-interval' ) }>
						{ __( 'Autosave Interval', 'syntatis-feature-flipper' ) }
					</label>
					<HelpTip>
						{ isLocked ? (
							<HelpContent>
								<p>
									{ __(
										'This setting is currently disabled, since it is manually managed by the "AUTOSAVE_INTERVAL" constant.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'To manage the autosave interval here, remove or update the constant.',
										'syntatis-feature-flipper'
									) }
								</p>
							</HelpContent>
						) : (
							<HelpContent readmore="https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#modify-autosave-interval">
								<p>
									{ __(
										'WordPress automatically saves changes while you edit posts and pages to help prevent data loss.',
										'syntatis-feature-flipper'
									) }
								</p>
								<p>
									{ __(
										'By default, an autosave is sent every 60 seconds. Shorter intervals reduce the amount of work lost if editing is interrupted, while longer intervals reduce the frequency of autosave requests.',
										'syntatis-feature-flipper'
									) }
								</p>
							</HelpContent>
						) }
					</HelpTip>
				</span>
			</th>
			<td>
				<Switch
					className={ styles.field }
					{ ...inputProps( 'autosave_interval_enabled' ) }
					defaultSelected={ isEnabled }
					onChange={ setEnabled }
					isDisabled={ isLocked || isDisabled }
					label={ __(
						'Use a custom autosave interval',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, WordPress uses its default interval of 60 seconds.',
						'syntatis-feature-flipper'
					) }
				/>
				{ isEnabled && ! isLocked && ! isDisabled && (
					<div className={ styles.details }>
						<TextField
							{ ...inputProps( 'autosave_interval' ) }
							type="number"
							min={ minInterval }
							max={ maxInterval }
							defaultValue={ interval }
							onChange={ ( value ) => {
								const parsedValue = Number( value );

								if ( Number.isFinite( parsedValue ) ) {
									setInterval( parsedValue );
								}
							} }
							suffix={ __(
								'seconds',
								'syntatis-feature-flipper'
							) }
							description={ __(
								'Shorter intervals can help reduce the amount of work lost if an editing session is interrupted, while longer intervals can reduce the frequency of autosave requests.',
								'syntatis-feature-flipper'
							) }
						/>
					</div>
				) }
			</td>
		</tr>
	);
};
