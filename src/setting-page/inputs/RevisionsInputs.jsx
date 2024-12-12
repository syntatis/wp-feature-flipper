import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { TextField } from '@syntatis/kubrick';
import { SwitchInput } from './SwitchInput';
import styles from './styles.module.scss';
import { useFormContext, useSettingsContext } from '../form';

export const RevisionsInputs = () => {
	const { getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ isEnabled, setEnabled ] = useState( getOption( 'revisions' ) );

	return (
		<SwitchInput
			name="revisions"
			id="revisions"
			title={ __( 'Revisions', 'syntatis-feature-flipper' ) }
			label={ __( 'Enable post revisions', 'syntatis-feature-flipper' ) }
			description={ __(
				'When switched off, WordPress will not save revisions of your posts.',
				'syntatis-feature-flipper'
			) }
			help={ __(
				'WordPress revisions automatically save backups of your posts and pages every time you make changes. It allows you to easily revert to previous versions if needed. While helpful for recovering content, storing too many revisions can clutter your database, slow down performance, and use up storage space. Limiting or disabling revisions can help to improve site efficiency, especially for multi-author blogs or sites with limited hosting resources.',
				'syntatis-feature-flipper'
			) }
			onChange={ setEnabled }
		>
			{ isEnabled && (
				<div className={ styles.inputDetails }>
					<TextField
						min={ 1 }
						max={ 100 }
						placeholder="-"
						defaultValue={ getOption( 'revisions_max' ) }
						type="number"
						name="revisions_max"
						onChange={ ( value ) => {
							setFieldsetValues( 'revisions_max', value );
						} }
						className="code"
						suffix={
							<span aria-hidden>
								{ __(
									'Revisions',
									'syntatis-feature-flipper'
								) }
							</span>
						}
						aria-label={ __(
							'Maximum',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'The maximum number of revisions to keep.',
							'syntatis-feature-flipper'
						) }
					/>
				</div>
			) }
		</SwitchInput>
	);
};
