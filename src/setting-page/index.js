import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import { SettingsProvider } from './components/form';
import { Page } from './Page';
import './index.css';

domReady( () => {
	const container = document.querySelector(
		'#syntatis-feature-flipper-settings'
	);
	if ( container ) {
		createRoot( container ).render(
			<SettingsProvider>
				<Page />
			</SettingsProvider>
		);
	}
} );
