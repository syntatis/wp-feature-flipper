import { __ } from '@wordpress/i18n';
import styles from './HelpContent.module.scss';
import { Link } from '@syntatis/kubrick';
import { external, Icon } from '@wordpress/icons';

export const HelpContent = ( { children, readmore } ) => {
	return (
		<div className={ styles.root }>
			{ children }
			{ readmore && (
				<p>
					<Link
						className={ styles.readmore }
						href={ readmore }
						target="_blank"
						suffix={ <Icon icon={ external } /> }
					>
						{ __( 'Read more', 'syntatis-feature-flipper' ) }
					</Link>
				</p>
			) }
		</div>
	);
};
