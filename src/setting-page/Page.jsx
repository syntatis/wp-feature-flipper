import { __ } from '@wordpress/i18n';
import { Tab, Tabs, TabsProvider } from '@syntatis/kubrick';
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
	return (
		<TabsProvider
			navigate
			url={ window.$syntatis.featureFlipper.settingPage }
		>
			<Tabs
				selectedKey={ window.$syntatis.featureFlipper.settingPageTab }
			>
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
		</TabsProvider>
	);
};
