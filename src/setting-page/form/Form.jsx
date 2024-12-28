import { isEqual } from 'lodash-es';
import { createContext } from '@wordpress/element';
import { SubmitButton } from './SubmitButton';
import { useSettingsContext } from './useSettingsContext';
import { FormNotice } from './FormNotice';
import styles from './Form.module.scss';

export const FormContext = createContext();

function setNestedValue( obj, keys, value ) {
	const key = keys.shift(); // Get the first key
	if ( ! keys.length ) {
		obj[ key ] = value; // If no more keys, set the value
	} else {
		if ( ! obj[ key ] ) {
			obj[ key ] = {}; // Initialize as an object if not already present
		}
		setNestedValue( obj[ key ], keys, value ); // Recurse for deeper levels
	}
}

/**
 * Parse and normalize data from form inputs into an object.
 *
 * Some of the data submitted from the form are nested in a string format,
 * for example: `key[nestedKey]`, `key[nestedKey][nestedKey]`, etc. But
 * unlike PHP which pre-processes the data, converts it to a nested
 * array, the `FormData` in JavaScript preserves the nested keys
 * as strings.
 *
 * This function is used to transform these keys and values into an object,
 * for example:
 *
 * {
 *    'key[a]': 'value',
 *    'key[b][c]': 'value
 * }
 *
 * ...will be normalized to:
 *
 * {
 *   key: {
 *      a: 'value',
 *      b: {
 *         c: 'value'
 *      }
 *   }
 * }
 *
 * @see https://www.php.net/manual/en/reserved.variables.post.php#87650
 *
 * @param {Object} input The input data from the form.
 */
function normalizeData( input ) {
	const output = {};
	for ( const [ key, value ] of Object.entries( input ) ) {
		const match = key.match( /^(\w+)(\[\w+\])+/ ); // Match keys with nested brackets
		if ( match ) {
			const baseKey = match[ 1 ]; // First part before brackets
			const nestedKeys = key
				.match( /\[(\w+)\]/g ) // Extract parts inside brackets
				.map( ( k ) => k.slice( 1, -1 ) ); // Remove brackets
			if ( ! output[ baseKey ] ) {
				output[ baseKey ] = {}; // Ensure base key exists
			}
			setNestedValue( output[ baseKey ], nestedKeys, value ); // Set the nested value
		} else {
			output[ key ] = value; // Directly assign non-nested keys
		}
	}

	return output;
}

export const Form = ( { children } ) => {
	const { submit, optionPrefix, values, setStatus } = useSettingsContext();

	return (
		<>
			<FormNotice />
			<form
				method="POST"
				className={ styles.root }
				onSubmit={ ( event ) => {
					event.preventDefault();

					/**
					 * @type {HTMLFormElement}
					 */
					const form = event.target;
					const formData = new FormData( form );
					let data = {};

					/**
					 * Compare the field values with the current values. If there are changes,
					 * submit only the new values. If there are no changes, do nothing.
					 */
					for ( const key in form.elements ) {
						const element = form.elements[ key ];
						const name = element.name;

						if (
							name === optionPrefix ||
							! name?.startsWith( optionPrefix )
						) {
							continue;
						}

						let value = null;

						switch ( element.type ) {
							case 'checkbox':
								const allValues = formData.getAll( name );

								if (
									element.getAttribute( 'value' ) === null
								) {
									value = element.checked ? true : false;
								} else {
									value = allValues;
								}

								if ( ! values[ name ] && ! value ) {
									continue;
								}

								break;

							case 'number':
								value = Number( formData.get( name ) );
								break;

							default:
								value = formData.get( name );
								break;
						}

						/**
						 * Convert other inputs which value may be a "number" but is not using the
						 * type="number" input.
						 */
						if (
							typeof value === 'string' &&
							/^-?\d+(\.\d+)?$/.test( value )
						) {
							value = Number( value );
						}

						data[ name ] = value;
					}

					data = normalizeData( data );

					for ( const [ name, value ] of Object.entries( data ) ) {
						if ( isEqual( value, values[ name ] ) ) {
							delete data[ name ];
						}
					}

					if ( Object.keys( data ).length === 0 ) {
						setStatus( 'no-change' );
						return;
					}

					submit( data );
				} }
			>
				{ children }
				<SubmitButton />
			</form>
		</>
	);
};
