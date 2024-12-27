/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import { RadioGroup } from '@syntatis/kubrick';
import { useSettingsContext, useFormContext } from '../form';
import { HelpTip } from '../components';
import styles from './styles.module.scss';

export const RadioGroupFieldset = ( {
	description,
	id,
	name,
	onChange,
	title,
	children,
	isDisabled,
	isSelected,
	help,
} ) => {
	const { labelProps, inputProps, getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();

	return (
		<tr>
			<th scope="row">
				<span className={ styles.label }>
					<label { ...labelProps( id ) }>{ title }</label>
					{ help && <HelpTip>{ help }</HelpTip> }
				</span>
			</th>
			<td>
				<RadioGroup
					{ ...inputProps( name ) }
					className={ styles.root }
					onChange={ ( checked ) => {
						if ( onChange !== undefined ) {
							onChange( checked );
						}
						setFieldsetValues( name, checked );
					} }
					defaultValue={ getOption( name ) }
					description={ description }
					isDisabled={ isDisabled }
					isSelected={ isSelected }
				>
					{ children }
				</RadioGroup>
			</td>
		</tr>
	);
};
