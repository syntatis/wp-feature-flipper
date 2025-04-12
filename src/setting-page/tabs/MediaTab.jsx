import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../form';
import {
	ImageQualityFieldset,
	RadioGroupFieldset,
	SwitchFieldset,
} from '../fieldset';
import { HelpContent } from '../components';

export const MediaTab = () => {
	return (
		<Form>
			<Fieldset>
				<SwitchFieldset
					name="attachment_page"
					id="attachment-page"
					title={ __(
						'Attachment Page',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable the attachment page',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, the attachment page for the media files will be disabled.',
						'syntatis-feature-flipper'
					) }
					help={
						<HelpContent>
							<p>
								{ __(
									"In WordPress, an attachment page is a dedicated page for each uploaded file, such as images or videos. It displays metadata like the file's title, description, and sometimes comments.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									"However, these pages often lack meaningful content, which can hurt SEO and confuse visitors, especially if the theme doesn't style them properly.",
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'Starting with WordPress 6.4, attachment pages are disabled by default for new WordPress site installations.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'When the attachment page is disabled, it will automatically redirect visitors to the homepage when they try to access it.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
				<SwitchFieldset
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
						'If switched off, the attachment page will get a randomized slug instead of taking from the original file name.',
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
			</Fieldset>
			<Fieldset title={ __( 'Library', 'syntatis-feature-flipper' ) }>
				<SwitchFieldset
					name="media_infinite_scroll"
					id="media-infinite-scroll"
					title={ __(
						'Infinite Scroll',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable the infinite scroll on the media library',
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
				<RadioGroupFieldset
					name="media_view_mode"
					id="media-view-mode"
					title={ __( 'View', 'syntatis-feature-flipper' ) }
					description={ __(
						'Select the layout mode to view images in the media library.',
						'syntatis-feature-flipper'
					) }
					options={ [
						{
							label: __(
								'Grid and List',
								'syntatis-feature-flipper'
							),
							value: 'both',
						},
						{
							label: __( 'Grid', 'syntatis-feature-flipper' ),
							value: 'grid',
						},
						{
							label: __( 'List', 'syntatis-feature-flipper' ),
							value: 'list',
						},
					] }
					help={
						<HelpContent>
							<p>
								{ __(
									'By default, you can switch between grid and list view in the media library. This setting allows you to select which mode to use when viewing the media library.',
									'syntatis-feature-flipper'
								) }
							</p>
							<p>
								{ __(
									'Selecting to either "Grid" or "List" will disable the ability to switch between views, so everyone will see the same view mode.',
									'syntatis-feature-flipper'
								) }
							</p>
						</HelpContent>
					}
				/>
			</Fieldset>
			<ImageQualityFieldset />
		</Form>
	);
};
