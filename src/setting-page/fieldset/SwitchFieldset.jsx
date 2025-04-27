/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import { Switch } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { HelpTip } from '../components';
import styles from './styles.module.scss';

export const SwitchFieldset = ( {
	description,
	id,
	label,
	name,
	onChange,
	title,
	children,
	isDisabled,
	isSelected,
	help,
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
				<Switch
					{ ...inputProps( name ) }
					className={ styles.field }
					onChange={ ( checked ) => {
						if ( onChange !== undefined ) {
							onChange( checked );
						}
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
