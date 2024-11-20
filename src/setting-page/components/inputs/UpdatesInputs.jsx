import { __ } from '@wordpress/i18n';
import { Checkbox, CheckboxGroup, Switch } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './UpdatesInputs.module.scss';
import { useState } from '@wordpress/element';

export const UpdatesInputs = () => {
	const { labelProps, inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ values, setValues ] = useState( {
		updates: getOption( 'updates' ),
		autoUpdate: getOption( 'auto_update' ),
	} );

	return (
		<tr>
			<th scope="row">
				<span>{ __( 'Updates', 'syntatis-feature-flipper' ) }</span>
			</th>
			<td>
				<div className={ styles.inputGroup }>
					<div className={ styles.inputGroup }>
						<Switch
							{ ...inputProps( 'updates' ) }
							onChange={ ( checked ) => {
								setValues( ( currentValues ) => {
									return {
										...currentValues,
										updates: checked,
										autoUpdate: checked,
									};
								} );
								setFieldsetValues( 'updates', checked );
								setFieldsetValues( 'auto_update', checked );
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
						{ values.updates && (
							<Switch
								{ ...inputProps( 'auto_update' ) }
								onChange={ ( checked ) => {
									setValues( ( currentValues ) => {
										return {
											...currentValues,
											autoUpdate: checked,
										};
									} );
									setFieldsetValues( 'auto_update', checked );
								} }
								defaultSelected={ getOption( 'auto_update' ) }
								description={ __(
									'Enable automatic updates for WordPress core, plugins, themes, and translations.',
									'syntatis-feature-flipper'
								) }
								label={ __(
									'Enable all automatic updates',
									'syntatis-feature-flipper'
								) }
							/>
						) }
					</div>
					{ values.updates && (
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
									{ values.autoUpdate && (
										<Checkbox
											label={ __(
												'Enable automatic update',
												'syntatis-feature-flipper'
											) }
										/>
									) }
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
									{ values.autoUpdate && (
										<Checkbox
											label={ __(
												'Enable automatic update',
												'syntatis-feature-flipper'
											) }
										/>
									) }
								</CheckboxGroup>
								<CheckboxGroup
									label={ __(
										'Themes',
										'syntatis-feature-flipper'
									) }
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
									{ values.autoUpdate && (
										<Checkbox
											label={ __(
												'Enable automatic update',
												'syntatis-feature-flipper'
											) }
										/>
									) }
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
									{ values.autoUpdate && (
										<Checkbox
											label={ __(
												'Automatic update',
												'syntatis-feature-flipper'
											) }
										/>
									) }
									<Checkbox
										label={ __(
											'Async update',
											'syntatis-feature-flipper'
										) }
									/>
								</CheckboxGroup>
							</div>
						</details>
					) }
				</div>
			</td>
		</tr>
	);
};
