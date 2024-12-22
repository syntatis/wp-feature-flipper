import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import { useId, useState } from '@wordpress/element';
import { Details } from '../components';

export const DashboardWidgetsInputs = () => {
	const { getOption, inputProps, inlineData } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'dashboard_widgets' )
	);
	const labelId = useId();
	const registeredWidgets = inlineData.wp.dashboardWidgets || [];
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
				'When switched off, all widgets will be hidden from the dashboard.',
				'syntatis-feature-flipper'
			) }
			onChange={ setEnabled }
		>
			{ isEnabled && (
				<Details
					summary={
						<span id={ labelId }>
							{ __( 'Settings', 'syntatis-feature-flipper' ) }
						</span>
					}
				>
					<CheckboxGroup
						defaultValue={ widgetsEnabled }
						label={ __( 'Widgets', 'syntatis-feature-flipper' ) }
						description={ __(
							'Unchecked widgets will be hidden from the dashboard.',
							'syntatis-feature-flipper'
						) }
						onChange={ ( value ) => {
							setFieldsetValues(
								'dashboard_widgets_enabled',
								value
							);
						} }
						{ ...inputProps( 'dashboard_widgets_enabled' ) }
					>
						{ Object.keys( registeredWidgets ).map( ( id ) => {
							return (
								<Checkbox
									key={ id }
									value={ registeredWidgets[ id ].id }
									label={ registeredWidgets[ id ].title }
								/>
							);
						} ) }
					</CheckboxGroup>
				</Details>
			) }
		</SwitchInput>
	);
};
