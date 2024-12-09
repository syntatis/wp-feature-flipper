/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import { Switch } from '@syntatis/kubrick';
import { useSettingsContext, useFormContext } from '../form';

export const SwitchInput = ( {
	description,
	id,
	label,
	name,
	onChange,
	title,
	children,
	isDisabled,
	isSelected,
} ) => {
	const { labelProps, inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();

	return (
		<tr>
			<th scope="row">
				<label { ...labelProps( id ) }>{ title }</label>
			</th>
			<td>
				<Switch
					{ ...inputProps( name ) }
					onChange={ ( checked ) => {
						if ( onChange !== undefined ) {
							onChange( checked );
						}
						setFieldsetValues( name, checked );
					} }
					defaultSelected={ getOption( name ) }
					description={ description }
					label={ label }
					isDisabled={ isDisabled }
					isSelected={ isSelected }
				/>
				{ children }
			</td>
		</tr>
	);
};
