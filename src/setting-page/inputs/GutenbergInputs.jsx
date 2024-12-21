import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { Details } from '../components';

const PostTypesInputs = () => {
	const postTypes = window.$syntatis?.featureFlipper?.postTypes;

	if ( ! postTypes ) {
		return null;
	}

	return (
		<CheckboxGroup
			name="gutenberg_post_types"
			description={ __(
				'Select the post types that will use the block editor.',
				'syntatis-feature-flipper'
			) }
			aria-label={ __(
				'Gutenberg Post Types',
				'syntatis-feature-flipper'
			) }
		>
			{ Object.keys( postTypes ).map( ( postTypeKey ) => {
				const postType = postTypes[ postTypeKey ];

				return (
					<Checkbox
						key={ postTypeKey }
						value={ postTypeKey }
						label={
							<span>
								{ postType.label } <code>{ postTypeKey }</code>
							</span>
						}
					/>
				);
			} ) }
		</CheckboxGroup>
	);
};

export const GutenbergInputs = () => {
	return (
		<SwitchInput
			name="gutenberg"
			id="gutenberg"
			title="Gutenberg"
			label={ __(
				'Enable the block editor',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'When switched off, the block editor will be disabled and the classic editor will be used.',
				'syntatis-feature-flipper'
			) }
		>
			<Details summary={ __( 'Post Types', 'syntatis-feature-flipper' ) }>
				<PostTypesInputs />
			</Details>
		</SwitchInput>
	);
};
