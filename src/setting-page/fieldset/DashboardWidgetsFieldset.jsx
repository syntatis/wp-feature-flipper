import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { useId, useState } from '@wordpress/element';
import { Details } from '../components';

export const DashboardWidgetsFieldset = () => {
	const { getOption, inputProps, inlineData } = useSettingsContext();
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'dashboard_widgets' )
	);
	const labelId = useId();
	const registeredWidgets = inlineData.$wp.dashboardWidgets || [];

	return (
		<SwitchFieldset
			name="dashboard_widgets"
			id="dashboard-widgets"
			title={ __( 'Dashboard Widgets', 'syntatis-feature-flipper' ) }
			label={ __(
				'Enable Dashboard widgets',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'If switched off, all widgets will be removed from the Dashboard.',
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
						defaultValue={ getOption(
							'dashboard_widgets_enabled'
						) }
						label={ __( 'Widgets', 'syntatis-feature-flipper' ) }
						description={ __(
							'Unchecked widgets will be removed from the Dashboard.',
							'syntatis-feature-flipper'
						) }
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
		</SwitchFieldset>
	);
};
