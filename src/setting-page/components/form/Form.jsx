import { createContext, useEffect, useRef, useState } from '@wordpress/element';
import { SubmitButton } from './SubmitButton';
import { useSettingsContext } from './useSettingsContext';
import { FormNotice } from './FormNotice';

export const FormContext = createContext();

export const Form = ( { children } ) => {
	const ref = useRef();
	const { submitValues, optionPrefix, values, setStatus } =
		useSettingsContext();
	const [ fieldsetValues, setFieldsetValues ] = useState( {} );

	useEffect( () => {
		if ( ! ref ) {
			return;
		}
		/**
		 * @type {HTMLFormElement}
		 */
		const form = ref.current;
		const fieldset = {};

		for ( const key in form.elements ) {
			const element = form.elements[ key ];
			if (
				element.name !== optionPrefix &&
				element.name?.startsWith( optionPrefix )
			) {
				fieldset[ element.name ] = values[ element.name ];
			}
		}

		setFieldsetValues( fieldset );
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [] );

	return (
		<FormContext.Provider
			value={ {
				setFieldsetValues: ( name, value ) => {
					setFieldsetValues( {
						...fieldsetValues,
						[ `${ optionPrefix }${ name }` ]: value,
					} );
				},
			} }
		>
			<form
				ref={ ref }
				method="POST"
				onSubmit={ ( event ) => {
					event.preventDefault();
					const submissions = {};

					for ( const key in fieldsetValues ) {
						if ( ! ( key in values ) ) {
							continue;
						}

						if ( values[ key ] === fieldsetValues[ key ] ) {
							continue;
						}

						submissions[ key ] = fieldsetValues[ key ];
					}

					if ( Object.keys( submissions ).length === 0 ) {
						setStatus( 'no-change' );
						return;
					}

					submitValues( submissions );
				} }
			>
				<FormNotice />
				{ children }
				<SubmitButton />
			</form>
		</FormContext.Provider>
	);
};
