import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import { useId } from '@wordpress/element';
import { Details, HelpContent } from '../components';

export const AdminBarInputs = () => {
	const { getOption, inputProps, inlineData } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const labelId = useId();
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
				'When switched off, the Admin bar will not be displayed on the front end.',
				'syntatis-feature-flipper'
			) }
			help={
				<HelpContent>
					<p>
						{ __(
							'When disabling the Admin Bar through this option, it will hide the Admin Bar on the front end only. The Admin Bar will still be visible in the admin area, and you will still be able to selectively hide which items are displayed on the Admin Bar.',
							'syntatis-feature-flipper'
						) }
					</p>
				</HelpContent>
			}
		>
			<Details
				summary={
					<span id={ labelId }>
						{ __( 'Menu', 'syntatis-feature-flipper' ) }
					</span>
				}
			>
				<CheckboxGroup
					defaultValue={
						getOption( 'admin_bar_menu' ) ??
						menu.map( ( { id } ) => id )
					}
					aria-labelledby={ labelId }
					description={ __(
						'Unchecked menu items will be hidden from the Admin bar.',
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
			</Details>
		</SwitchInput>
	);
};
