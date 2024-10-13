import { __ } from '@wordpress/i18n';
import { Button, Spinner } from '@syntatis/kubrick';
import { useSettingsContext } from './useSettingsContext';

export const SubmitButton = () => {
	const { updating } = useSettingsContext();

	return (
		<div className="submit">
			<Button
				isDisabled={ updating }
				prefix={ updating && <Spinner /> }
				type="submit"
			>
				{ updating
					? __( 'Saving Changes', 'syntatis-feature-flipper' )
					: __( 'Save Changes', 'syntatis-feature-flipper' ) }
			</Button>
		</div>
	);
};
