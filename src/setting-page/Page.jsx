import { __ } from '@wordpress/i18n';
import { Tab, Tabs } from '@syntatis/kubrick';
import { useSettingsContext } from './components/form';
import {
	AdminTab,
	AssetsTab,
	GeneralTab,
	MediaTab,
	SecurityTab,
	WebpageTab,
} from './tabs';
import '@syntatis/kubrick/dist/index.css';

export const Page = () => {
	const { setStatus } = useSettingsContext();
	return (
		<Tabs onSelectionChange={ () => setStatus( null ) }>
			<Tab
				key="general"
				title={ __( 'General', 'syntatis-feature-flipper' ) }
			>
				<GeneralTab />
			</Tab>
			<Tab
				key="admin"
				title={ __( 'Admin', 'syntatis-feature-flipper' ) }
			>
				<AdminTab />
			</Tab>
			<Tab
				key="media"
				title={ __( 'Media', 'syntatis-feature-flipper' ) }
			>
				<MediaTab />
			</Tab>
			<Tab
				key="assets"
				title={ __( 'Assets', 'syntatis-feature-flipper' ) }
			>
				<AssetsTab />
			</Tab>
			<Tab
				key="webpage"
				title={ __( 'Webpage', 'syntatis-feature-flipper' ) }
			>
				<WebpageTab />
			</Tab>
			<Tab
				key="security"
				title={ __( 'Security', 'syntatis-feature-flipper' ) }
			>
				<SecurityTab />
			</Tab>
		</Tabs>
	);
};
