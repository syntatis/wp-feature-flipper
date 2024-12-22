import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { SettingsProvider } from './form';
import { Page } from './Page';

domReady( () => {
	const container = document.querySelector(
		'#syntatis-feature-flipper-settings'
	);
	if ( container ) {
		createRoot( container ).render(
			<SettingsProvider inlineData={ window.$syntatis }>
				<Page />
			</SettingsProvider>
		);
	}
} );
