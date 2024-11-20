import { __ } from '@wordpress/i18n';
import { Checkbox, CheckboxGroup, Switch } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './UpdatesInputs.module.scss';
import { useState } from '@wordpress/element';

export const UpdatesInputs = () => {
	const { labelProps, inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ isUpdatesEnabled, setUpdatesEnabled ] = useState(
		getOption( 'updates' )
	);

	return (
		<tr>
			<th scope="row">
				<span>{ __( 'Updates', 'syntatis-feature-flipper' ) }</span>
			</th>
			<td>
				<div className={ styles.inputGroup }>
					<Switch
						{ ...inputProps( 'updates' ) }
						onChange={ ( checked ) => {
							setFieldsetValues( 'updates', checked );
						} }
						defaultSelected={ getOption( 'updates' ) }
						label={ __(
							'Enable all updates',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'Enable updates for WordPress core, plugins, themes, and translations.',
							'syntatis-feature-flipper'
						) }
					/>
					<Switch
						{ ...inputProps( 'auto_update' ) }
						onChange={ ( checked ) => {
							setFieldsetValues( 'updates', checked );
						} }
						defaultSelected={ getOption( 'updates' ) }
						description={ __(
							'Enable automatic updates for WordPress core, plugins, themes, and translations.',
							'syntatis-feature-flipper'
						) }
						label={ __(
							'Enable all automatic updates',
							'syntatis-feature-flipper'
						) }
					/>
				</div>
				<details className={ styles.inputDetails }>
					<summary>
						{ __( 'Settings', 'syntatis-feature-flipper' ) }
					</summary>
					<div className={ styles.inputGroup }>
						<CheckboxGroup
							label={ __(
								'WordPress',
								'syntatis-feature-flipper'
							) }
							description={ __(
								'WordPress core update configurations.',
								'syntatis-feature-flipper'
							) }
						>
							<Checkbox
								label={ __(
									'Enable update',
									'syntatis-feature-flipper'
								) }
							/>
							<Checkbox
								label={ __(
									'Enable automatic update',
									'syntatis-feature-flipper'
								) }
							/>
						</CheckboxGroup>
						<CheckboxGroup
							label={ __(
								'Plugins',
								'syntatis-feature-flipper'
							) }
							description={ __(
								'Plugins update configurations.',
								'syntatis-feature-flipper'
							) }
						>
							<Checkbox
								label={ __(
									'Enable update',
									'syntatis-feature-flipper'
								) }
							/>
							<Checkbox
								label={ __(
									'Enable automatic update',
									'syntatis-feature-flipper'
								) }
							/>
						</CheckboxGroup>
						<CheckboxGroup
							label={ __( 'Themes', 'syntatis-feature-flipper' ) }
							description={ __(
								'Themes update configuration.',
								'syntatis-feature-flipper'
							) }
						>
							<Checkbox
								label={ __(
									'Enable update',
									'syntatis-feature-flipper'
								) }
							/>
							<Checkbox
								label={ __(
									'Enable automatic update',
									'syntatis-feature-flipper'
								) }
							/>
						</CheckboxGroup>
						<CheckboxGroup
							label={ __(
								'Translations',
								'syntatis-feature-flipper'
							) }
							description={ __(
								'Translations update configuration.',
								'syntatis-feature-flipper'
							) }
						>
							<Checkbox
								label={ __(
									'Automatic update',
									'syntatis-feature-flipper'
								) }
							/>
							<Checkbox
								label={ __(
									'Async update',
									'syntatis-feature-flipper'
								) }
							/>
						</CheckboxGroup>
					</div>
				</details>
			</td>
		</tr>
	);
};
