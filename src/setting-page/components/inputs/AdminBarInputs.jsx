import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './AdminBarInputs.module.scss';
import { useId } from '@wordpress/element';

export const AdminBarInputs = () => {
	const { getOption, inputProps, inlineData } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const summaryId = useId();
	const menu = inlineData.adminBarMenu || [];

	return (
		<SwitchInput
			name="admin_bar"
			id="admin-bar"
			title={ __( 'Admin Bar', 'syntatis-feature-flipper' ) }
			label={ __(
				'Show the Admin bar on the front end',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'When set to "off", the admin bar will not be shown on the front end.',
				'syntatis-feature-flipper'
			) }
		>
			<details className={ styles.menuDetails }>
				<summary id={ summaryId }>
					<strong>
						{ __( 'Menu', 'syntatis-feature-flipper' ) }
					</strong>
				</summary>
				<CheckboxGroup
					defaultValue={
						getOption( 'admin_bar_menu' ) ??
						menu.map( ( { id } ) => id )
					}
					aria-labelledby={ summaryId }
					description={ __(
						'List of menu items registered to admin bar.',
						'syntatis-feature-flipper'
					) }
					onChange={ ( value ) =>
						setFieldsetValues( 'admin_bar_menu', value )
					}
					{ ...inputProps( 'admin_bar_menu' ) }
				>
					{ menu.map( ( { id } ) => (
						<Checkbox
							key={ id }
							value={ id }
							label={ <code>{ id }</code> }
						/>
					) ) }
				</CheckboxGroup>
			</details>
		</SwitchInput>
	);
};
