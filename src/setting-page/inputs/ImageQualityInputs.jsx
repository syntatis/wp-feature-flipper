import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { TextField } from '@syntatis/kubrick';
import { Fieldset, useFormContext, useSettingsContext } from '../form';
import { useState } from '@wordpress/element';

export const ImageQualityInputs = () => {
	const { getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ values, setValues ] = useState( {
		jpegCompression: getOption( 'jpeg_compression' ),
	} );

	return (
		<Fieldset
			title={ __( 'Image Quality', 'syntatis-feature-flipper' ) }
			description={ __(
				'Image quality and compression settings for uploaded images.',
				'syntatis-feature-flipper'
			) }
		>
			<SwitchInput
				name="jpeg_compression"
				id="jpeg-compression"
				title="JPEG"
				label={ __(
					'Enable JPEG image compression',
					'syntatis-feature-flipper'
				) }
				description={ __(
					'If switched off, WordPress will upload the original JPEG image in its full quality, without any compression.',
					'syntatis-feature-flipper'
				) }
				onChange={ ( value ) => {
					setValues( ( currentValues ) => {
						return {
							...currentValues,
							jpegCompression: value,
						};
					} );
				} }
			>
				{ values.jpegCompression && (
					<div style={ { marginTop: '1rem' } }>
						<TextField
							min={ 10 }
							max={ 100 }
							type="number"
							name="jpeg_compression_quality"
							defaultValue={ getOption(
								'jpeg_compression_quality'
							) }
							onChange={ ( value ) => {
								setFieldsetValues(
									'jpeg_compression_quality',
									value
								);
							} }
							className="code"
							prefix={
								<span aria-hidden>
									{ __(
										'Quality',
										'syntatis-feature-flipper'
									) }
								</span>
							}
							aria-label={ __(
								'Quality',
								'syntatis-feature-flipper'
							) }
							suffix="%"
						/>
					</div>
				) }
			</SwitchInput>
		</Fieldset>
	);
};
