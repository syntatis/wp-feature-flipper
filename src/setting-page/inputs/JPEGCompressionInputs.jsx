import { __ } from '@wordpress/i18n';
import { SwitchInput } from './SwitchInput';
import { TextField } from '@syntatis/kubrick';
import { useFormContext, useSettingsContext } from '../form';
import styles from './styles.module.scss';
import { useState } from '@wordpress/element';

export const JPEGCompressionInputs = () => {
	const { getOption } = useSettingsContext();
	const { setFieldsetValues } = useFormContext();
	const [ isEnabled, setEnabled ] = useState(
		getOption( 'jpeg_compression' )
	);

	return (
		<SwitchInput
			name="jpeg_compression"
			id="jpeg-compression"
			title={ __( 'JPEG Compression', 'syntatis-feature-flipper' ) }
			label={ __(
				'Enable JPEG image compression',
				'syntatis-feature-flipper'
			) }
			description={ __(
				'When switched off, WordPress will upload the original JPEG image in its full quality, without any compression.',
				'syntatis-feature-flipper'
			) }
			onChange={ setEnabled }
		>
			{ isEnabled && (
				<div className={ styles.inputDetails }>
					<TextField
						min={ 10 }
						max={ 100 }
						type="number"
						name="jpeg_compression_quality"
						defaultValue={ getOption( 'jpeg_compression_quality' ) }
						onChange={ ( value ) => {
							setFieldsetValues(
								'jpeg_compression_quality',
								value
							);
						} }
						className="code"
						prefix={
							<span aria-hidden>
								{ __( 'Quality', 'syntatis-feature-flipper' ) }
							</span>
						}
						aria-label={ __(
							'Quality',
							'syntatis-feature-flipper'
						) }
						description={ __(
							'The quality of the compressed JPEG image. 100 is the highest quality.',
							'syntatis-feature-flipper'
						) }
						suffix="%"
					/>
				</div>
			) }
		</SwitchInput>
	);
};
