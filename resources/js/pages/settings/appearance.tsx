import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import { LanguageSwitcher } from '@/components/language-switcher';
import { useBreadcrumbs } from '@/hooks/use-breadcrumbs';
import { useTranslation } from '@/hooks/use-translation';
import appLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { edit as editAppearance } from '@/routes/appearance';

export default function Appearance() {
    const { t } = useTranslation();

    useBreadcrumbs([
        {
            title: t('settings.appearance_settings'),
            href: editAppearance(),
        },
    ]);

    return (
        <>
            <Head title={t('settings.appearance_settings')} />

            <h1 className="sr-only">{t('settings.appearance_settings')}</h1>

            <SettingsLayout>
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={t('settings.appearance_settings')}
                        description={t('settings.appearance_description')}
                    />
                    <AppearanceTabs />
                </div>

                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={t('settings.language')}
                        description={t('settings.language_description')}
                    />
                    <LanguageSwitcher />
                </div>
            </SettingsLayout>
        </>
    );
}

Appearance.layout = appLayout;
