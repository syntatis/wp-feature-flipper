/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import { Radio, RadioGroup } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
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
	options,
	orientation,
} ) => {
	const { labelProps, inputProps, getOption } = useSettingsContext();

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
					} }
					defaultValue={ getOption( name ) }
					description={ description }
					isDisabled={ isDisabled }
					isSelected={ isSelected }
					orientation={ orientation }
				>
					{ options.map( ( { label, value, ...props } ) => (
						<Radio key={ value } value={ value } { ...props }>
							{ label }
						</Radio>
					) ) }
				</RadioGroup>
				{ children }
			</td>
		</tr>
	);
};
