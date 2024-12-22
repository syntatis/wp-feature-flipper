import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { EnvironmentType } from './EnvironmentType/EnvironmentType';

domReady( () => {
	const container = document.querySelector(
		'#fb6e5b8410f1e6ee52ac3e663f4f6c58-root'
	);
	if ( container ) {
		const data = JSON.parse( container.dataset.inline );

		createRoot( container ).render(
			<EnvironmentType environmentType={ data.environmentType } />
		);
	}
} );
