import { useContext } from '@wordpress/element';
import { SettingsContext } from './SettingsProvider';

export const useSettingsContext = () => {
	const context = useContext( SettingsContext );

	if ( ! context ) {
		throw new Error(
			'useSettingsContext must be used within a SettingsProvider'
		);
	}

	return context;
};
