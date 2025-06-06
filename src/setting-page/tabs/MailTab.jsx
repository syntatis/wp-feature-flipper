import { __ } from '@wordpress/i18n';
import { Fieldset, Form, useSettingsContext } from '../form';
import { SwitchFieldset, TextFieldset } from '../fieldset';

export const MailTab = () => {
	const { inlineData } = useSettingsContext();
	const host = new URL( inlineData.$wp.siteUrl ).hostname.trimStart( 'www.' );

	return (
		<Form>
			<Fieldset>
				<SwitchFieldset
					name="mail_sending"
					id="mail-sending"
					title={ __( 'Sending', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable sending emails',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'If switched off, WordPress will not send any emails.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
			<Fieldset
				title={ __( 'Attributes', 'syntatis-feature-flipper' ) }
				description={ __(
					'Settings to customize the email attributes used by WordPress to send emails.',
					'syntatis-feature-flipper'
				) }
			>
				<TextFieldset
					className="regular-text code"
					name="mail_from_address"
					id="mail-from-address"
					type="email"
					placeholder={ `wordpress@${ host }` }
					title={ __( 'From Address', 'syntatis-feature-flipper' ) }
					description={ __(
						'Apply custom email address to use when WordPress sends emails.',
						'syntatis-feature-flipper'
					) }
					validateBehavior="native"
				/>
				<TextFieldset
					className="regular-text code"
					name="mail_from_name"
					id="mail-from-name"
					placeholder="WordPress"
					title={ __( 'From Name', 'syntatis-feature-flipper' ) }
					description={ __(
						'Apply custom name to use when WordPress sends emails.',
						'syntatis-feature-flipper'
					) }
					validateBehavior="native"
				/>
			</Fieldset>
		</Form>
	);
};
