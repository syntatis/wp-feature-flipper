import { __, sprintf } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { TextField } from '@syntatis/kubrick';
import { SwitchFieldset } from './SwitchFieldset';
import { useSettingsContext } from '../form';
import { HelpContent } from '../components';
import styles from './styles.module.scss';

export const RevisionsFieldset = () => {
	const { getOption, getOptionName } = useSettingsContext();
	const [ isEnabled, setEnabled ] = useState( getOption( 'revisions' ) );
	const revisionMax = Number( getOption( 'revisions_max' ) );
	const isUnlimited =
		! Number.isFinite( revisionMax ) || revisionMax <= 0;

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
				<div className={ styles.details }>
					<TextField
						type="number"
						min={ 1 }
						max={ 100 }
						name={ getOptionName( 'revisions_max' ) }
						defaultValue={ isUnlimited ? '' : revisionMax }
						placeholder={ isUnlimited ? '∞' : undefined }
						aria-label={ __(
							'Maximum revisions',
							'syntatis-feature-flipper'
						) }
						validationBehavior="aria"
						validate={ ( value ) => {
							if ( value === '' ) {
								return undefined;
							}

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
						suffix={ __(
							'revisions',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'Leave empty to keep all revisions, or enter the maximum number of revisions to keep.',
							'syntatis-feature-flipper'
						) }
					/>
				</div>
			) }
		</SwitchFieldset>
	);
};
