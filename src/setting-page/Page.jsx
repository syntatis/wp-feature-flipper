import { __ } from '@wordpress/i18n';
import { Tab, Tabs, TabsProvider } from '@syntatis/kubrick';
import {
	AdminTab,
	AdvancedTab,
	GeneralTab,
	MediaTab,
	SecurityTab,
	SiteTab,
} from './tabs';
import { useSettingsContext } from './form';
import './Page.scss';

export const Page = () => {
	const { inlineData } = useSettingsContext();

	return (
		<TabsProvider navigate url={ inlineData.featureFlipper?.settingPage }>
			<Tabs
				selectedKey={
					inlineData.featureFlipper?.settingPageTab || undefined
				}
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
					key="site"
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
				<Tab
					key="advanced"
					title={ __( 'Advanced', 'syntatis-feature-flipper' ) }
				>
					<AdvancedTab />
				</Tab>
			</Tabs>
		</TabsProvider>
	);
};
