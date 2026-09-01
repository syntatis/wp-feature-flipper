import { __, sprintf } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { TextField } from '@syntatis/kubrick';
import { SwitchFieldset } from './SwitchFieldset';
import { Fieldset, useSettingsContext } from '../form';
import { HelpContent } from '../components';

const MIN_THRESHOLD = 1;
const MAX_THRESHOLD = 10000;

export const ImageSizesFieldset = () => {
	const { getOption, getOptionName } = useSettingsContext();
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'big_image_size' )
	);

	return (
		<Fieldset
			title={ __( 'Image Sizes', 'syntatis-feature-flipper' ) }
			description={ __(
				'Settings to control the size of the uploaded images.',
				'syntatis-feature-flipper'
			) }
		>
			<SwitchFieldset
				name="big_image_size"
				id="big-image-size"
				title={ __( 'Big Image Size', 'syntatis-feature-flipper' ) }
				label={ __(
					'Enable big image size threshold',
					'syntatis-feature-flipper'
				) }
				description={ __(
					'If switched off, WordPress will not scale down images that exceed the specified threshold.',
					'syntatis-feature-flipper'
				) }
				onChange={ setEnabled }
				help={
					<HelpContent
						readmore="https://developer.wordpress.org/reference/hooks/big_image_size_threshold/"
					>
						<p>
							{ __(
								'Since WordPress 5.3, images wider or taller than the threshold are automatically scaled down when uploaded, while the original image is kept in the uploads directory.',
								'syntatis-feature-flipper'
							) }
						</p>
						<p>
							{ __(
								'The default of 2560px is recommended for most sites. Disabling the automatic scaling, or raising the threshold, preserves very large images as-is. This may increase storage usage, slow down uploads, and deliver larger files to visitors.',
								'syntatis-feature-flipper'
							) }
						</p>
						<p>
							{ __(
								'Changing this setting does not resize images that have already been uploaded.',
								'syntatis-feature-flipper'
							) }
						</p>
					</HelpContent>
				}
			>
				{ isEnabled && (
					<div style={ { marginTop: '1rem' } }>
						<TextField
							type="number"
							min={ MIN_THRESHOLD }
							max={ MAX_THRESHOLD }
							step={ 1 }
							validationBehavior="aria"
							validate={ ( value ) => {
								const parsedValue = Number( value );

								if (
									! Number.isFinite( parsedValue ) ||
									parsedValue < MIN_THRESHOLD
								) {
									return sprintf(
										/* translators: %s: The minimum big image size threshold in pixels. */
										__(
											'The value must be at least %s px.',
											'syntatis-feature-flipper'
										),
										MIN_THRESHOLD,
									);
								}

								if ( parsedValue > MAX_THRESHOLD ) {
									return sprintf(
										/* translators: %s: The maximum big image size threshold in pixels. */
										__(
											'The value must be at most %s px.',
											'syntatis-feature-flipper'
										),
										MAX_THRESHOLD,
									);
								}

								return undefined;
							} }
							name={ getOptionName( 'big_image_size_threshold' ) }
							defaultValue={ getOption(
								'big_image_size_threshold'
							) }
							className="code"
							prefix={
								<span aria-hidden>
									{ __(
										'Threshold',
										'syntatis-feature-flipper'
									) }
								</span>
							}
							suffix={ __( 'px', 'syntatis-feature-flipper' ) }
							aria-label={ __(
								'Big image threshold in pixels',
								'syntatis-feature-flipper'
							) }
						/>
					</div>
				) }
			</SwitchFieldset>
		</Fieldset>
	);
};
