import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './AdminBarInputs.module.scss';

export const AdminBarInputs = ( { menu = [] } ) => {
	const { getOption, inputProps } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();

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
			<CheckboxGroup
				className={ styles.adminBarMenu }
				defaultValue={
					getOption( 'admin_bar_menu' ) ??
					menu.map( ( { id } ) => id )
				}
				label={ __( 'Menu', 'syntatis-feature-flipper' ) }
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
		</SwitchInput>
	);
};
