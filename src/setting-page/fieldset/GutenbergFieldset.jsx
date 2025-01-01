import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { useState } from '@wordpress/element';
import { Checkbox, CheckboxGroup } from '@syntatis/kubrick';
import { Details } from '../components';
import { useSettingsContext } from '../form';

const PostTypesInputs = () => {
	const { getOption, inlineData, inputProps } = useSettingsContext();
	const postTypes = inlineData.$wp.postTypes;

	if ( ! postTypes ) {
		return null;
	}

	for ( const postTypeKey in postTypes ) {
		if ( ! postTypes[ postTypeKey ].supports?.editor ) {
			delete postTypes[ postTypeKey ];
			continue;
		}
	}

	const postTypesValues = getOption( 'gutenberg_post_types' );
	const postTypesSelected =
		postTypesValues === null ? Object.keys( postTypes ) : postTypesValues;

	return (
		<CheckboxGroup
			defaultValue={ postTypesSelected }
			description={ __(
				'Select the post types that will use the block editor.',
				'syntatis-feature-flipper'
			) }
			label={ __( 'Post Types', 'syntatis-feature-flipper' ) }
			{ ...inputProps( 'gutenberg_post_types' ) }
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
	const { getOption } = useSettingsContext();
	const [ value, setValue ] = useState( getOption( 'gutenberg' ) );

	return (
		<SwitchFieldset
			name="gutenberg"
			onChange={ setValue }
			id="gutenberg"
			title={ __( 'Block Editor', 'syntatis-feature-flipper' ) }
			label={ __(
				'Enable the block editor (a.k.a Gutenberg)',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'If switched off, the block editor will be disabled and the classic editor will be used.',
				'syntatis-feature-flipper'
			) }
		>
			{ value && (
				<Details
					summary={ __( 'Settings', 'syntatis-feature-flipper' ) }
				>
					<PostTypesInputs />
				</Details>
			) }
		</SwitchFieldset>
	);
};
