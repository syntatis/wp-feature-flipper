import { Button, Tooltip } from '@syntatis/kubrick';
import { __ } from '@wordpress/i18n';
import styles from './EnvironmentType.module.scss';

const ENVIRONMENT_TYPES_LABELS = {
	local: __( 'Local', 'syntatis-feature-flipper' ),
	development: __( 'Development', 'syntatis-feature-flipper' ),
	staging: __( 'Staging', 'syntatis-feature-flipper' ),
	production: __( 'Production', 'syntatis-feature-flipper' ),
};

const ENVIRONMENT_TYPES_DESCRIPTIONS = {
	local: __(
		'You\'re currently working on the "Local" environment of the site. It typically runs on a local computer or local server for development, testing, and debugging purposes.',
		'syntatis-feature-flipper'
	),
	development: __(
		'You\'re currently working on the "Development" environment of the site, typically used for developing, testing, and debugging before it is launched on the staging platform. This environment is usually remotely accessed using SSH or SFTP.',
		'syntatis-feature-flipper'
	),
	staging: __(
		'You\'re currently working on the "Staging" environment of the site. This environment typically closely mimics the production environment, used for validating any modifications or upgrades before they are applied to the live site.',
		'syntatis-feature-flipper'
	),
	production: __(
		'You\'re currently working on the "Production" site. The is the live environment where public users can access the site. Maintaining its stability and security is critical. Please be cautious when making changes on the site.',
		'syntatis-feature-flipper'
	),
};

export const EnvironmentType = ( { environmentType } ) => {
	if ( ! environmentType ) {
		return null;
	}
	return (
		<Tooltip
			className={ styles.tooltip }
			content={
				<p>{ ENVIRONMENT_TYPES_DESCRIPTIONS[ environmentType ] }</p>
			}
			placement="bottom"
		>
			<Button
				prefix={
					<span className="dashicons dashicons-cloud ab-icon" />
				}
				className={ styles.trigger }
			>
				{ ENVIRONMENT_TYPES_LABELS[ environmentType ] }
			</Button>
		</Tooltip>
	);
};
