import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { EnvironmentType } from './EnvironmentType/EnvironmentType';

domReady( () => {
	const container = document.querySelector(
		'#syntatis-feature-flipper-environment-type'
	);
	if ( container ) {
		const data = JSON.parse( container.dataset.inline );

		createRoot( container ).render(
			<EnvironmentType environmentType={ data.environmentType } />
		);
	}
} );
