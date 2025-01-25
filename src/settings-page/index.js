import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { SettingsProvider } from './form';
import { Page } from './Page';

domReady( () => {
	const container = document.querySelector(
		'#syntatis-feature-flipper-settings'
	);
	if ( container ) {
		const inlineData = JSON.parse( container.dataset.inline );
		const nonceData = container.dataset.nonce;

		createRoot( container ).render(
			<SettingsProvider inlineData={ inlineData } nonceData={ nonceData }>
				<Page />
			</SettingsProvider>
		);
	}
} );
