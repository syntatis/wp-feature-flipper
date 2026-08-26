/**
 * Compare two version strings.
 *
 * @param {string} a First version.
 * @param {string} b Second version.
 * @return {number} -1 when `a` is lower, 0 when equal, 1 when `a` is higher.
 */
export const compareVersions = ( a, b ) => {
	const partsA = String( a )
		.split( '.' )
		.map( ( part ) => parseInt( part, 10 ) || 0 );
	const partsB = String( b )
		.split( '.' )
		.map( ( part ) => parseInt( part, 10 ) || 0 );
	const length = Math.max( partsA.length, partsB.length );

	for ( let i = 0; i < length; i++ ) {
		const diff = ( partsA[ i ] ?? 0 ) - ( partsB[ i ] ?? 0 );

		if ( diff !== 0 ) {
			return diff > 0 ? 1 : -1;
		}
	}

	return 0;
};
