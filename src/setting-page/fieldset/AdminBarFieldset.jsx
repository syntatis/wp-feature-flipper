import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { Details, HelpContent } from '../components';

export const AdminBarFieldset = () => {
	const { getOption, inputProps, inlineData } = useSettingsContext();
	const menu = inlineData.$wp.adminBarMenu || [];

	return (
		<SwitchFieldset
			name="admin_bar"
			id="admin-bar"
			title={ __( 'Admin Bar', 'syntatis-feature-flipper' ) }
			label={ __(
				'Show the Admin bar on the front end',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'If switched off, the Admin bar will not be displayed on the front end.',
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
			<Details summary={ __( 'Settings', 'syntatis-feature-flipper' ) }>
				<CheckboxGroup
					defaultValue={
						getOption( 'admin_bar_menu' ) ??
						menu.map( ( id ) => id )
					}
					label={ __( 'Menu', 'syntatis-feature-flipper' ) }
					description={ __(
						'Unchecked menu items will be hidden from the Admin bar.',
						'syntatis-feature-flipper'
					) }
					{ ...inputProps( 'admin_bar_menu' ) }
				>
					{ menu.map( ( id ) => (
						<Checkbox
							key={ id }
							value={ id }
							label={ <code>{ id }</code> }
						/>
					) ) }
				</CheckboxGroup>
			</Details>
		</SwitchFieldset>
	);
};
