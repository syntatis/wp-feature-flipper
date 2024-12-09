import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import useSessionState from 'use-session-storage-state';

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

export const useSettings = ( { optionPrefix } ) => {
	const [ values, setValues ] = useState( preloaded );
	const [ updatedValues, setUpdatedValues ] = useState();
	const [ updating, setUpdating ] = useState( false );
	const [ errorMessages, setErrorMessages ] = useState( {} );
	const [ status, setStatus ] = useSessionState( `${ optionPrefix }status`, {
		defaultValue: null,
	} );
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

	useEffect( () => {
		if ( status === null ) {
			return;
		}
		if ( status.startsWith( '__' ) ) {
			setStatus( status.replace( '__', '' ) );
		} else {
			setStatus( null );
		}
	}, [] );

	const submitValues = ( data ) => {
		setUpdating( true );
		apiFetch( {
			path: '/wp/v2/settings',
			method: 'POST',
			data,
		} )
			.then( ( response ) => {
				setValues( filterValues( response ) );
				setStatus( '__success' );
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
				setStatus( '__error' );
			} )
			.finally( () => {
				setUpdatedValues( data );
				document.querySelector( 'body' ).scrollIntoView();
				window.location.reload();
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
		updatedValues,
	};
};
