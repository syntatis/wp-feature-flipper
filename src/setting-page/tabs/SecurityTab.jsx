import { __ } from '@wordpress/i18n';
import { Fieldset, Form } from '../components/form';
import { SwitchInput } from '../components/inputs';

const themeEditors = document.querySelector(
	'#adminmenu a[href="theme-editor.php"]'
);
const pluginEditors = document.querySelector(
	'#adminmenu a[href="plugin-editor.php"]'
);
const originalDisplay = {
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
					title={ __( 'File Edit', 'syntatis-feature-flipper' ) }
					label={ __(
						'Enable the File Editor',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", it will disable the file editor for themes and plugins.',
						'syntatis-feature-flipper'
					) }
					onChange={ ( checked ) => {
						if ( themeEditors ) {
							themeEditors.parentElement.style.display = ! checked
								? 'none'
								: originalDisplay.themeEditors;
						}
						if ( pluginEditors ) {
							pluginEditors.parentElement.style.display =
								! checked
									? 'none'
									: originalDisplay.pluginEditors;
						}
					} }
				/>
				<SwitchInput
					name="xmlrpc"
					id="xmlrpc"
					title={ __( 'XML-RPC', 'syntatis-feature-flipper' ) }
					label={ __( 'Enable XML-RPC', 'syntatis-feature-flipper' ) }
					description={ __(
						'When set to "off", it will remove and disable the XML-RPC endpoint.',
						'syntatis-feature-flipper'
					) }
				/>
				<SwitchInput
					name="authenticated_rest_api"
					id="authenticated-rest-api"
					title={ __(
						'REST API Authentication',
						'syntatis-feature-flipper'
					) }
					label={ __(
						'Enable REST API Authentication',
						'syntatis-feature-flipper'
					) }
					description={ __(
						'When set to "off", it will allow users to make request to the public REST API endpoint without authentication.',
						'syntatis-feature-flipper'
					) }
				/>
			</Fieldset>
		</Form>
	);
};
