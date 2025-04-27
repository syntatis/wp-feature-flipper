/* eslint-disable jsx-a11y/label-has-associated-control -- Handled by the `labelProps` */
import clsx from 'clsx/lite';
import { TextField } from '@syntatis/kubrick';
import { useSettingsContext } from '../form';
import { HelpTip } from '../components';
import styles from './styles.module.scss';

export const TextFieldset = ( {
	description,
	id,
	label,
	name,
	title,
	children,
	isDisabled,
	isReadOnly,
	placeholder,
	help,
	type = 'text',
	className,
	validate,
	validateBehavior,
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
				<TextField
					{ ...inputProps( name ) }
					type={ type }
					className={ clsx( styles.field, className ) }
					defaultValue={ getOption( name ) }
					placeholder={ placeholder }
					description={ description }
					label={ label }
					isDisabled={ isDisabled }
					isReadOnly={ isReadOnly }
					validate={ validate }
					validationBehavior={ validateBehavior }
				/>
				{ children }
			</td>
		</tr>
	);
};
