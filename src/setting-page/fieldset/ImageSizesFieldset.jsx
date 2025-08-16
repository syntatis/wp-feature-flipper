import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { TextField } from '@syntatis/kubrick';
import { Fieldset, useSettingsContext } from '../form';
import { useState } from '@wordpress/element';

export const ImageSizesFieldset = () => {
	const { getOption, getOptionName } = useSettingsContext();
	const [ values, setValues ] = useState( {
		bigImageSize: getOption( 'big_image_size' ),
	} );

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
				title="Big Image Size"
				label={ __(
					'Enable big image size threshold',
					'syntatis-feature-flipper'
				) }
				description={ __(
					'If switched off, WordPress will not scale down images that exceed the specified threshold.',
					'syntatis-feature-flipper'
				) }
				onChange={ ( value ) => {
					setValues( ( currentValues ) => {
						return {
							...currentValues,
							bigImageSize: value,
						};
					} );
				} }
			>
				{ values.bigImageSize && (
					<div style={ { marginTop: '1rem' } }>
						<TextField
							min={ 10 }
							max={ 9999 }
							type="number"
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
							aria-label={ __(
								'The threshold big image threshold size',
								'syntatis-feature-flipper'
							) }
							suffix="px"
						/>
					</div>
				) }
			</SwitchFieldset>
		</Fieldset>
	);
};
