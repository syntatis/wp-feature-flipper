import { Notice } from '@syntatis/kubrick';
import { __ } from '@wordpress/i18n';
import { useSettingsContext } from './useSettingsContext';

const messages = {
	success: __( 'Settings saved.', 'syntatis-feature-flipper' ),
	error: __( 'Settings failed to save.', 'syntatis-feature-flipper' ),
	'no-change': __( 'No changes were made.', 'syntatis-feature-flipper' ),
};

const levels = {
	success: 'success',
	error: 'error',
	'no-change': 'info',
};

function getNoticeMessage( status ) {
	return messages[ status ] || '';
}

function getNoticeLevel( status ) {
	return levels[ status ] || 'info';
}

export const FormNotice = () => {
	const { updating, status, setStatus } = useSettingsContext();

	if ( updating || ! status || status.startsWith( '__' ) ) {
		return null;
	}

	const message = getNoticeMessage( status );

	return (
		message && (
			<Notice
				isDismissable
				level={ getNoticeLevel( status ) }
				onDismiss={ () => setStatus( null ) }
			>
				<strong>{ message }</strong>
			</Notice>
		)
	);
};
