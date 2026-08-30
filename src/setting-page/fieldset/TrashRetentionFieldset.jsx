import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Switch, TextField } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { HelpContent, HelpTip } from '../components';
import styles from './styles.module.scss';

export const TrashRetentionFieldset = () => {
	const { getOption, getOptionName, labelProps, inputProps, inlineData } =
		useSettingsContext();

	const trashRetention = inlineData.features?.trashRetention || {};
	const isLocked = trashRetention.isLocked === true;
	const lockedDays = Number( trashRetention.days );
	const defaultDays = Number( trashRetention.defaultDays );
	const maxDays = Number( trashRetention.maxDays );

	const option = getOption( 'trash_retention' );
	const parsed = Number( option );
	const initialDays =
		Number.isFinite( parsed ) && parsed > 0 ? parsed : defaultDays;
	const [ days, setDays ] = useState( initialDays );
	const [ isEnabled, setEnabled ] = useState( option !== 0 );

	const isSelected = isLocked ? lockedDays !== 0 : isEnabled;

	return (
		<tr>
			<th scope="row">
				<span className={ styles.label }>
					<label { ...labelProps( 'trash-retention' ) }>
						{ __( 'Trash Retention', 'syntatis-feature-flipper' ) }
					</label>
					<HelpTip>
						{ isLocked ? (
							<HelpContent>
								<p>
									{ __(
											'This setting is currently disabled, since it is manually managed by the "EMPTY_TRASH_DAYS" constant.',
											'syntatis-feature-flipper'
										) }
								</p>
								<p>
									{ __(
											'To manage Trash retention here, remove or update the constant.',
											'syntatis-feature-flipper'
										) }
								</p>
							</HelpContent>
						) : (
							<HelpContent readmore="https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#empty-trash">
								<p>
									{ __(
											'WordPress keeps deleted posts and comments in the Trash for 30 days by default.',
											'syntatis-feature-flipper'
										) }
								</p>
								<p>
									{ __(
											'You can change how long items remain in the Trash before they are permanently deleted, or disable the Trash so that deleted content is removed immediately.',
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
					onChange={ setEnabled }
					isSelected={ isSelected }
					isDisabled={ isLocked }
					label={ __(
						'Enable Trash retention',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, deleted content is permanently removed instead of being moved to the Trash.',
						'syntatis-feature-flipper'
					) }
				/>
				{ isSelected ? (
					<div className={ styles.details }>
						<TextField
							{ ...inputProps( 'trash_retention' ) }
							type="number"
							min={ 1 }
							max={ maxDays }
							defaultValue={ isLocked ? lockedDays : days }
							onChange={ ( value ) => {
								const parsedValue = Number( value );

								if (
									Number.isFinite( parsedValue ) &&
									parsedValue >= 1
								) {
									setDays( parsedValue );
								}
							} }
							isDisabled={ isLocked }
							suffix={ __( 'days', 'syntatis-feature-flipper' ) }
							description={
								__(
									'Choose how long deleted content should remain in the Trash before it is permanently deleted.',
									'syntatis-feature-flipper'
								)
							}
						/>
					</div>
				) : (
					! isLocked && (
						<input
							type="hidden"
							name={ getOptionName( 'trash_retention' ) }
							value="0"
						/>
					)
				) }
			</td>
		</tr>
	);
};
