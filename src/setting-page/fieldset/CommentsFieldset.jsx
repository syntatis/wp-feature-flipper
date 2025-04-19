import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { useState } from '@wordpress/element';
import { Checkbox, TextField } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import styles from './CommentsFieldset.module.scss';

export const CommentsFieldset = () => {
	const { getOption, getOptionName } = useSettingsContext();
	const [ isEnabled, setEnabled ] = useState( getOption( 'comments' ) );

	const minCharsEnabled = getOption( 'comment_min_length_enabled' );
	const minChars = getOption( 'comment_min_length' );
	const [ isMinEnabled, setMinEnabled ] = useState( minCharsEnabled );

	return (
		<SwitchFieldset
			name="comments"
			id="comments"
			title={ __( 'Comments', 'syntatis-feature-flipper' ) }
			label={ __( 'Enable comments', 'syntatis-feature-flipper' ) }
			description={ __(
				'If switched off, comments will be disabled site-wide.',
				'syntatis-feature-flipper'
			) }
			onChange={ setEnabled }
		>
			{ isEnabled && (
				<div className={ styles.details }>
					<Checkbox
						label={ __(
							'Minimum length:',
							'syntatis-feature-flipper'
						) }
						name={ getOptionName( 'comment_min_length_enabled' ) }
						onChange={ setMinEnabled }
						defaultSelected={ isMinEnabled }
						suffix={
							<TextField
								className={ `${ styles.minInput } code` }
								min={ 1 }
								name={ getOptionName( 'comment_min_length' ) }
								defaultValue={ minChars }
								type="number"
								isReadOnly={ ! isMinEnabled }
								suffix={ __(
									'characters',
									'syntatis-feature-flipper'
								) }
							/>
						}
						description={ __(
							'The minimum number of characters required to post a comment.',
							'syntatis-feature-flipper'
						) }
					/>
				</div>
			) }
		</SwitchFieldset>
	);
};
