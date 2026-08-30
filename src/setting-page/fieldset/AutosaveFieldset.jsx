import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { HelpContent } from '../components';

export const AutosaveFieldset = ( { onChange } ) => {
	return (
		<SwitchFieldset
			name="autosave"
			id="autosave"
			onChange={ onChange }
			title={ __( 'Autosave', 'syntatis-feature-flipper' ) }
			label={ __( 'Enable autosave', 'syntatis-feature-flipper' ) }
			description={ __(
				'If switched off, WordPress will not automatically save your post and page edits.',
				'syntatis-feature-flipper'
			) }
			help={
				<HelpContent readmore="https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#modify-autosave-interval">
					<p>
						{ __(
							'WordPress automatically saves changes while you edit posts and pages to help prevent data loss.',
							'syntatis-feature-flipper'
						) }
					</p>
					<p>
						{ __(
							'Disabling autosave means unsaved changes could be lost if your browser crashes or your connection drops.',
							'syntatis-feature-flipper'
						) }
					</p>
				</HelpContent>
			}
		/>
	);
};
