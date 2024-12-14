import { __ } from '@wordpress/i18n';
import { Tab, Tabs, TabsProvider } from '@syntatis/kubrick';
import {
	AdminTab,
	AssetsTab,
	GeneralTab,
	MediaTab,
	SecurityTab,
	SiteTab,
} from './tabs';
import { useSettingsContext } from './form';

export const Page = () => {
	const { inlineData } = useSettingsContext();

	return (
		<TabsProvider navigate url={ inlineData.settingPage }>
			<Tabs selectedKey={ inlineData.settingPageTab || undefined }>
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
					title={ __( 'Site', 'syntatis-feature-flipper' ) }
				>
					<SiteTab />
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
