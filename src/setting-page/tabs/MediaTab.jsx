import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import { ImageQualityInputs, SwitchInput } from '../inputs';
import { HelpContent } from '../components';

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
						'If switched off, WordPress will not create attachment pages for media files.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									"An attachment page in WordPress is a standalone page for each uploaded file, like images or videos, showing metadata such as the file's title, description, and sometimes comments.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"These pages often lack valuable content, which can negatively affect SEO and confuse visitors, especially if themes don't format them well.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'When disabled, the attachment pages will be redirected to the homepage.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
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
						'If switched off, attachment page will get a randomized slug instead of taking from the original file name.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'By default, WordPress will generate the attachment page slug based on the file name. This could cause conflicts with existing or future post or page slugs, as the slug may already be reserved and WordPress would append a suffix (like -2) to the post slug to make it unique.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'When disabled, WordPress will generate a random slug for the attachment page.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
				<SwitchInput
					name="media_infinite_scroll"
					id="media-infinite-scroll"
					title={ __(
						'Infinite Scroll',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable the infinite scroll for media library',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the media library will use the "Load More" button instead of infinite scrolling.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									'Starting with WordPress 5.8, infinite scrolling is disabled by default in the Media Library. This setting allows you to reenable it.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
			<ImageQualityInputs />
		</Form>
	);
};
