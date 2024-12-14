import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { EnvironmentType } from './EnvironmentType/EnvironmentType';
import '@syntatis/kubrick/dist/index.css';

domReady( () => {
	const container = document.querySelector(
		'#syntatis-feature-flipper-environment-type-root'
	);
	if ( container ) {
		createRoot( container ).render(
			<EnvironmentType
				environmentType={
					window.$syntatis.featureFlipper.environmentType
				}
			/>
		);
	}
} );
