import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

export const MediaTab = () => {
	return (
		<Form>
			<Fieldset
				description={ __(
					'Control the behavior of media files uploaded to your site.',
					'syntatis-feature-flipper'
				) }
			>
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
						'When set to "off", WordPress will not create attachment pages for media files.',
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
						'When set to "off", media files will get a randomized slug instead of taking from the original file name.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="jpeg_compression"
					id="jpeg-compression"
					title={ __(
						'JPEG Compression',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable JPEG image compression',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", WordPress will upload the original JPEG image in its full quality, without any compression.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
