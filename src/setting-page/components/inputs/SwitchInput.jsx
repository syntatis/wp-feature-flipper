/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import { Switch } from '@syntatis/kubrick';
import { useSettingsContext, useFormContext } from '../form';

export const SwitchInput = ( {
	name,
	label,
	id,
	description,
	children,
	onChange,
} ) => {
	const { labelProps, inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();

	return (
		<tr>
			<th scope="row">
				<label { ...labelProps( id ) }>{ label }</label>
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
					label={ children }
				/>
			</td>
		</tr>
	);
};
