import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { JPEGCompressionInputs, SwitchInput } from '../inputs';

export const MediaTab = () => {
	return (
		<Form>
			<Fieldset>
				<SwitchInput
					name="attachment_page"
					id="attachment-page"
					title={ __(
						'Attachment Page',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable page for uploaded media files',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, WordPress will not create attachment pages for media files.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="attachment_slug"
					id="attachment-slug"
					title={ __(
						'Attachment Slug',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable default media file slug',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When switched off, attachment page will get a randomized slug instead of taking from the original file name.',
						'syntatis-feature-flipper'
					) }
				/>
				<JPEGCompressionInputs />
			</Fieldset>
		</Form>
	);
};
