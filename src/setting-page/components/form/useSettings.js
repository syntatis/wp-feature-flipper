import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

const preloaded = await apiFetch( {
	path: '/wp/v2/settings',
	method: 'GET',
} );

function parseExceptionMessage( errorString ) {
	const regex = /: \[(.*?)\] (.+) in/;
	const match = errorString?.match( regex );

	if ( match ) {
		return { [ match[ 1 ] ]: match[ 2 ] };
	}

	return null;
}

export const useSettings = () => {
	const [ values, setValues ] = useState( preloaded );
	const [ status, setStatus ] = useState();
	const [ updating, setUpdating ] = useState( false );
	const [ errorMessages, setErrorMessages ] = useState( {} );
	const filterValues = ( v ) => {
		for ( const name in v ) {
			if ( ! Object.keys( values ).includes( name ) ) {
				delete v[ name ];
			}
		}

		return v;
	};

	/**
	 * @param {string} name The option name.
	 */
	function getOption( name ) {
		return values[ name ];
	}

	useEffect( () => {
		if ( updating ) {
			setErrorMessages( {} );
		}
	}, [ updating ] );

	const submitValues = ( data ) => {
		setUpdating( true );
		apiFetch( {
			path: '/wp/v2/settings',
			method: 'POST',
			data,
		} )
			.then( ( response ) => {
				setValues( filterValues( response ) );
				setStatus( 'success' );
			} )
			.catch( ( response ) => {
				const errorMessage = parseExceptionMessage(
					response?.data?.error?.message
				);
				setErrorMessages( ( currentErrorMessages ) => {
					if ( ! errorMessage ) {
						return;
					}

					return { ...currentErrorMessages, ...errorMessage };
				} );
				setStatus( 'error' );
			} )
			.finally( () => {
				setUpdating( false );
			} );
	};

	return {
		values,
		status,
		errorMessages,
		updating,
		submitValues,
		setValues,
		setStatus,
		getOption,
		initialValues: preloaded,
	};
};
