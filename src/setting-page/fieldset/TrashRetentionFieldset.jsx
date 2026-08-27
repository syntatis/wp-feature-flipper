import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Checkbox, TextField } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { HelpContent, HelpTip } from '../components';
import styles from './styles.module.scss';

const DEFAULT_DAYS = 30;
const MAX_DAYS = 3650;

export const TrashRetentionFieldset = () => {
	const { getOption, labelProps, inputProps, inlineData } = useSettingsContext();

	const trashRetention = inlineData.features?.trashRetention || {};
	const isLocked = trashRetention.isLocked === true;
	const lockedDays = Number( trashRetention.days );

	const option = getOption( 'trash_retention' );
	const parsed = Number( option );
	const initialDays =
		Number.isFinite( parsed ) && parsed > 0 ? parsed : DEFAULT_DAYS;
	const [ days, setDays ] = useState( initialDays );
	const [ isDisabled, setDisabled ] = useState( option === 0 );

	const isChecked = isLocked ? lockedDays === 0 : isDisabled;

	let fieldValue = days;
	let fieldKey = 'trash-retention-enabled';

	if ( isLocked ) {
		fieldValue = lockedDays;
		fieldKey = 'trash-retention-locked';
	} else if ( isDisabled ) {
		fieldValue = 0;
		fieldKey = 'trash-retention-disabled';
	}

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
										'This setting is managed by the EMPTY_TRASH_DAYS constant defined in your wp-config.php file.',
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
				<TextField
					key={ fieldKey }
					{ ...inputProps( 'trash_retention' ) }
					type="number"
					min={ 1 }
					max={ MAX_DAYS }
					defaultValue={ fieldValue }
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
					isReadOnly={ ! isLocked && isDisabled }
					suffix={ __( 'days', 'syntatis-feature-flipper' ) }
					description={
						isLocked
							? __(
									'This setting is managed by the EMPTY_TRASH_DAYS constant in wp-config.php.',
									'syntatis-feature-flipper'
							  )
							: __(
									'Choose how long deleted content should remain in the Trash before it is permanently deleted.',
									'syntatis-feature-flipper'
							  )
					}
				/>
				<div style={ { marginTop: '1rem' } }>
					<Checkbox
						onChange={ setDisabled }
						isSelected={ isChecked }
						isDisabled={ isLocked }
						label={ __(
							'Disable Trash',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'Items deleted while Trash is disabled are permanently deleted and cannot be restored.',
							'syntatis-feature-flipper'
						) }
					/>
				</div>
			</td>
		</tr>
	);
};
