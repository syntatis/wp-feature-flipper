import { __ } from '@wordpress/i18n';
import { SwitchFieldset } from './SwitchFieldset';
import { TextField } from '@syntatis/kubrick';
import { Fieldset, useSettingsContext } from '../form';
import { useState } from '@wordpress/element';

export const ImageQualityFieldset = () => {
	const { getOption, getOptionName } = useSettingsContext();
	const [ values, setValues ] = useState( {
		jpegCompression: getOption( 'jpeg_compression' ),
	} );

	return (
		<Fieldset
			title={ __( 'Image Quality', 'syntatis-feature-flipper' ) }
			description={ __(
				'Settings to control the quality of the uploaded images.',
				'syntatis-feature-flipper'
			) }
		>
			<SwitchFieldset
				name="jpeg_compression"
				id="jpeg-compression"
				title="JPEG"
				label={ __(
					'Enable JPEG image compression',
					'syntatis-feature-flipper'
				) }
				description={ __(
					'If switched off, the image with JPEG format will be uploaded with the original quality.',
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
							name={ getOptionName( 'jpeg_compression_quality' ) }
							defaultValue={ getOption(
								'jpeg_compression_quality'
							) }
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
			</SwitchFieldset>
		</Fieldset>
	);
};
