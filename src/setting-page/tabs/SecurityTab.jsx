import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

const themeEditors = document.querySelector(
	'#adminmenu a[href="theme-editor.php"]'
);
const pluginEditors = document.querySelector(
	'#adminmenu a[href="plugin-editor.php"]'
);
const orginalDisplay = {
	themeEditors: themeEditors?.parentElement?.style?.display,
	pluginEditors: pluginEditors?.parentElement?.style?.display,
};

export const SecurityTab = () => {
	return (
		<Form>
			<Fieldset>
				<SwitchInput
					name="file_edit"
					id="file-edit"
					label={ __( 'File Edit', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", it will disable the file editor for themes and plugins.',
						'syntatis-feature-flipper'
					) }
					onChange={ ( checked ) => {
						if ( themeEditors ) {
							themeEditors.parentElement.style.display = ! checked
								? 'none'
								: orginalDisplay.themeEditors;
						}
						if ( pluginEditors ) {
							pluginEditors.parentElement.style.display =
								! checked
									? 'none'
									: orginalDisplay.pluginEditors;
						}
					} }
				>
					{ __( 'Enable File Editor', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="xmlrpc"
					id="xmlrpc"
					label={ __( 'XML-RPC', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", it will remove and disable the XML-RPC endpoint.',
						'syntatis-feature-flipper'
					) }
				>
					{ __( 'Enable XML-RPC', 'syntatis-feature-flipper' ) }
				</SwitchInput>
				<SwitchInput
					name="authenticated_rest_api"
					id="authenticated-rest-api"
					label={ __(
						'REST API Authentication',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", it will allow users to make request to the public REST API endpoint without authentication.',
						'syntatis-feature-flipper'
					) }
				>
					{ __(
						'Enable REST API Authentication',
						'syntatis-feature-flipper'
					) }
				</SwitchInput>
			</Fieldset>
		</Form>
	);
};
