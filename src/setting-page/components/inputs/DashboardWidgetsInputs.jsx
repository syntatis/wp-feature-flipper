import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './DashboardWidgetsInputs.module.scss';
import { useState } from '@wordpress/element';

export const DashboardWidgetsInputs = ( { widgets = [] } ) => {
	const { getOption, inputProps } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'dashboard_widgets' )
	);
	const widgetsEnabled = getOption( 'dashboard_widgets_enabled' ) ?? null;

	return (
		<SwitchInput
			name="dashboard_widgets"
			id="dashboard-widgets"
			title={ __( 'Dashboard Widgets', 'syntatis-feature-flipper' ) }
			label={ __(
				'Enable Dashboard widgets',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'Customize the widgets to show or hide on the WordPress dashboard.',
				'syntatis-feature-flipper'
			) }
			onChange={ setEnabled }
		>
			{ isEnabled && (
				<CheckboxGroup
					className={ styles.widgetsEnabled }
					defaultValue={ widgetsEnabled }
					label={ __( 'Widgets', 'syntatis-feature-flipper' ) }
					description={ __(
						'List of widgets registered to the dashboard.',
						'syntatis-feature-flipper'
					) }
					onChange={ ( value ) => {
						setFieldsetValues( 'dashboard_widgets_enabled', value );
					} }
					{ ...inputProps( 'dashboard_widgets_enabled' ) }
				>
					{ widgets.map( ( { id, title } ) => {
						return (
							<Checkbox key={ id } value={ id } label={ title } />
						);
					} ) }
				</CheckboxGroup>
			) }
		</SwitchInput>
	);
};
