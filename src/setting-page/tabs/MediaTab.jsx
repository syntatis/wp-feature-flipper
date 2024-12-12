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
					help={ __(
						"An attachment page in WordPress is a standalone page for each uploaded file, like images or videos, showing metadata such as the file's title, description, and sometimes comments. These pages often lack valuable content, which can negatively affect SEO and confuse visitors, especially if themes don't format them well. When disabled, the attachment pages will be redirected to the homepage.",
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
					help={ __(
						'By default, WordPress will generate the attachment page slug based on the file name. This could cause conflicts with existing or future post or page slugs, as the slug may already be reserved and WordPress would append a suffix (like -2) to the post slug to make it unique. When disabled, WordPress will generate a random slug for the attachment page.',
						'syntatis-feature-flipper'
					) }
				/>
				<JPEGCompressionInputs />
			</Fieldset>
		</Form>
	);
};
