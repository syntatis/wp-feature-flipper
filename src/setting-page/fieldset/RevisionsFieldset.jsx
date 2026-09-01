import { __, sprintf } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Checkbox, TextField } from '@syntatis/kubrick';
import { SwitchFieldset } from './SwitchFieldset';
import { useSettingsContext } from '../form';
import { HelpContent } from '../components';
import styles from './RevisionsFieldset.module.scss';

export const RevisionsFieldset = () => {
	const { getOption, getOptionName } = useSettingsContext();
	const [ isEnabled, setEnabled ] = useState( getOption( 'revisions' ) );
	const [ isMaxEnabled, setMaxEnabled ] = useState(
		getOption( 'revisions_max_enabled' )
	);
	const revisionMax = getOption( 'revisions_max' );

	return (
		<SwitchFieldset
			name="revisions"
			id="revisions"
			title={ __( 'Revisions', 'syntatis-feature-flipper' ) }
			label={ __( 'Enable post revisions', 'syntatis-feature-flipper' ) }
			description={ __(
				'If switched off, revisions of your posts will not be saved.',
				'syntatis-feature-flipper'
			) }
			help={
				<HelpContent readmore="https://wordpress.org/documentation/article/revisions/">
					<p>
						{ __(
							'While the revision feature is helpful for recovering content, storing too many revisions can clutter the database, slow down performance, and use up storage space.',
							'syntatis-feature-flipper'
						) }
					</p>
					<p>
						{ __(
							'Limiting or disabling revisions can help to improve your site database more efficient, especially for multi-author blogs or sites with limited hosting resources.',
							'syntatis-feature-flipper'
						) }
					</p>
				</HelpContent>
			}
			onChange={ setEnabled }
		>
			{ isEnabled && (
				<div style={ { marginTop: '1rem' } }>
					<Checkbox
						className={ styles.option }
						label={ __(
							'Maximum revisions:',
							'syntatis-feature-flipper'
						) }
						name={ getOptionName( 'revisions_max_enabled' ) }
						onChange={ setMaxEnabled }
						defaultSelected={ isMaxEnabled }
						suffix={
							<TextField
								min={ 1 }
								max={ 100 }
								validationBehavior="aria"
								validate={ ( value ) => {
									const parsedValue = Number( value );

									if (
										! Number.isFinite( parsedValue ) ||
										parsedValue < 1
									) {
										return sprintf(
											/* translators: %s: The minimum number of revisions. */
											__(
												'The value must be at least %s revisions.',
												'syntatis-feature-flipper'
											),
											1,
										);
									}

									if ( parsedValue > 100 ) {
										return sprintf(
											/* translators: %s: The maximum number of revisions. */
											__(
												'The value must be at most %s revisions.',
												'syntatis-feature-flipper'
											),
											100,
										);
									}

									return undefined;
								} }
								placeholder={
									typeof revisionMax === 'number'
										? revisionMax
										: '∞'
								}
								defaultValue={ revisionMax }
								type="number"
								name={ getOptionName( 'revisions_max' ) }
								className="code"
								aria-label={ __(
									'Maximum',
									'syntatis-feature-flipper'
								) }
								isReadOnly={ ! isMaxEnabled }
							/>
						}
						description={ __(
							'Apply maximum number of revisions to keep.',
							'syntatis-feature-flipper'
						) }
					/>
				</div>
			) }
		</SwitchFieldset>
	);
};
