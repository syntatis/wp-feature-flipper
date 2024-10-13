import { useContext } from '@wordpress/element';
import { FormContext } from './Form';

export const useFormContext = () => {
	const context = useContext( FormContext );

	if ( ! context ) {
		throw new Error( 'useFormContext must be used within a Fieldset' );
	}

	return context;
};
