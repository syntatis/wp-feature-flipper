import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { Checkbox, TextField } from '@syntatis/kubrick';
import { SwitchFieldset } from './SwitchFieldset';
import { useSettingsContext } from '../form';
import { HelpContent } from '../components';

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
