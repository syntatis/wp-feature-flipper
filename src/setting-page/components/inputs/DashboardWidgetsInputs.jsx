import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './DashboardWidgetsInputs.module.scss';
import { useId, useState } from '@wordpress/element';

export const DashboardWidgetsInputs = () => {
	const { getOption, inputProps, inlineData } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'dashboard_widgets' )
	);
	const labelId = useId();
	const widgets = inlineData.dashboardWidgets || [];
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
				<details className={ styles.widgetsDetails }>
					<summary>
						<strong id={ labelId }>
							{ __( 'Widgets', 'syntatis-feature-flipper' ) }
						</strong>
					</summary>
					<CheckboxGroup
						defaultValue={ widgetsEnabled }
						aria-labelledby={ labelId }
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
						{ widgets.map( ( { id, title } ) => {
							return (
								<Checkbox
									key={ id }
									value={ id }
									label={ title }
								/>
							);
						} ) }
					</CheckboxGroup>
				</details>
			) }
		</SwitchInput>
	);
};
