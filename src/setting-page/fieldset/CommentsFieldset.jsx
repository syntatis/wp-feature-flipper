import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { useState } from '@wordpress/element';
import { Checkbox, TextField } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import styles from './CommentsFieldset.module.scss';

export const CommentsFieldset = () => {
	const { getOption, optionPrefix } = useSettingsContext();
	const [ isEnabled, setEnabled ] = useState( getOption( 'comments' ) );
	const [ isMinEnabled, setMinEnabled ] = useState(
		getOption( 'comment_minchars_enabled' )
	);

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
							'Set minimum',
							'syntatis-feature-flipper'
						) }
						onChange={ setMinEnabled }
						suffix={
							<TextField
								suffix={ __(
									'characters',
									'syntatis-feature-flipper'
								) }
								className={ `${ styles.minInput } code` }
								min={ 10 }
								name={ `${ optionPrefix }comment_minchars` }
								aria-label={ __(
									'Minimum characters',
									'syntatis-feature-flipper'
								) }
								defaultValue={ getOption( 'comment_minchars' ) }
								type="number"
								isDisabled={ ! isMinEnabled }
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
