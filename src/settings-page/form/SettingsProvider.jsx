import { createContext } from '@wordpress/element';
import { useSettings } from './useSettings';

export const SettingsContext = createContext();
export const SettingsProvider = ( { children, inlineData, nonceData } ) => {
	const optionPrefix = 'syntatis_feature_flipper_';
	const attrPrefix = 'syntatis-feature-flipper-';
	const {
		status,
		updating,
		values,
		errorMessages,
		setStatus,
		setValues,
		submit,
		getOption,
		initialValues,
		updatedValues,
	} = useSettings( {
		optionPrefix,
		nonceData,
	} );

	return (
		<SettingsContext.Provider
			value={ {
				errorMessages,
				status,
				updating,
				values,
				optionPrefix,
				setStatus,
				submit,
				initialValues,
				inlineData,
				updatedValues,
				setValues: ( name, value ) => {
					setValues( {
						...values,
						[ `${ optionPrefix }${ name }` ]: value,
					} );
				},
				getOption: ( name ) => {
					return getOption( `${ optionPrefix }${ name }` );
				},
				labelProps: ( id ) => {
					return {
						htmlFor: `${ attrPrefix }${ id }`,
						id: `${ attrPrefix }${ id }-label`,
					};
				},
				inputProps: ( name ) => {
					const id = name.replaceAll( '_', '-' );
					return {
						'aria-labelledby': `${ attrPrefix }${ id }-label`,
						id: `${ attrPrefix }${ id }`,
						name: `${ optionPrefix }${ name }`,
					};
				},
			} }
		>
			{ children }
		</SettingsContext.Provider>
	);
};
