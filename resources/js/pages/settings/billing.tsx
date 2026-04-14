import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import BillingController from '@/actions/App/Http/Controllers/Settings/BillingController';
import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { useBreadcrumbs } from '@/hooks/use-breadcrumbs';
import { useTranslation } from '@/hooks/use-translation';
import appLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { show as showBilling } from '@/routes/billing';

type CheckoutOptions = {
    settings?: Record<string, unknown>;
    items: Array<Record<string, unknown>>;
    customer?: Record<string, unknown>;
    customData?: Record<string, unknown>;
};

type BillingPlan = {
    name: string;
    description: string;
    prices: {
        monthly: string;
        yearly: string;
    };
};

type BillingProps = {
    has_internal_access: boolean;
    has_billing_access: boolean;
    on_active_trial: boolean;
    trial_ends_at: string | null;
    plans: Record<string, BillingPlan>;
    subscription: {
        status: string | null;
        subscribed: boolean;
        on_grace_period: boolean;
        current_price_id: string | null;
        ends_at: string | null;
        trial_ends_at: string | null;
    };
};

type PageProps = {
    billing: BillingProps;
    flash: {
        success: string | null;
        error: string | null;
    };
};

const PLAN_PRICES = {
    starter: {
        monthly: 'EUR 6 / month',
        yearly: 'EUR 48 / year',
    },
    max: {
        monthly: 'EUR 8 / month',
        yearly: 'EUR 72 / year',
    },
} as const;

export default function BillingSettings() {
    const { t } = useTranslation();
    const { billing, flash } = usePage<PageProps>().props;
    const [checkoutError, setCheckoutError] = useState<string | null>(null);

    useBreadcrumbs([
        {
            title: t('settings.billing'),
            href: showBilling(),
        },
    ]);

    return (
        <>
            <Head title={t('settings.billing')} />

            <h1 className="sr-only">{t('settings.billing')}</h1>

            <SettingsLayout>
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={t('billing.title')}
                        description={t('billing.description')}
                    />

                    {flash.error ? (
                        <Alert variant="destructive">
                            <AlertTitle>{t('common.error')}</AlertTitle>
                            <AlertDescription>{flash.error}</AlertDescription>
                        </Alert>
                    ) : null}

                    {flash.success ? (
                        <Alert>
                            <AlertTitle>{t('common.success')}</AlertTitle>
                            <AlertDescription>{flash.success}</AlertDescription>
                        </Alert>
                    ) : null}

                    {checkoutError ? (
                        <Alert variant="destructive">
                            <AlertTitle>{t('common.error')}</AlertTitle>
                            <AlertDescription>{checkoutError}</AlertDescription>
                        </Alert>
                    ) : null}

                    {billing.has_internal_access ? (
                        <Alert>
                            <AlertTitle>{t('billing.internal_access_title')}</AlertTitle>
                            <AlertDescription>{t('billing.internal_access_description')}</AlertDescription>
                        </Alert>
                    ) : null}

                    <SubscriptionStatus billing={billing} />

                    <div className="grid gap-4 md:grid-cols-2">
                        {Object.entries(billing.plans).map(([planKey, plan]) => (
                            <PlanCard
                                key={planKey}
                                planKey={planKey}
                                plan={plan}
                                billing={billing}
                                onCheckoutError={setCheckoutError}
                            />
                        ))}
                    </div>
                </div>
            </SettingsLayout>
        </>
    );
}

BillingSettings.layout = appLayout;

function SubscriptionStatus({ billing }: { billing: BillingProps }) {
    const { t } = useTranslation();

    return (
        <Card>
            <CardHeader>
                <CardTitle>{t('billing.subscription_status')}</CardTitle>
                <CardDescription>{t('billing.subscription_status_description')}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-3">
                <div className="flex flex-wrap items-center gap-2">
                    <Badge variant={billing.has_billing_access ? 'default' : 'secondary'}>
                        {billing.on_active_trial
                            ? t('billing.trial_active')
                            : (billing.subscription.status ?? t('billing.no_subscription'))}
                    </Badge>
                    {billing.subscription.on_grace_period ? (
                        <Badge variant="secondary">{t('billing.grace_period')}</Badge>
                    ) : null}
                </div>

                {billing.on_active_trial && billing.trial_ends_at ? (
                    <p className="text-sm text-muted-foreground">
                        {t('billing.trial_ends_at', {
                            date: new Date(billing.trial_ends_at).toLocaleDateString(),
                        })}
                    </p>
                ) : null}

                {billing.subscription.trial_ends_at ? (
                    <p className="text-sm text-muted-foreground">
                        {t('billing.trial_ends_at', {
                            date: new Date(billing.subscription.trial_ends_at).toLocaleDateString(),
                        })}
                    </p>
                ) : null}

                {billing.subscription.ends_at ? (
                    <p className="text-sm text-muted-foreground">
                        {t('billing.access_until', {
                            date: new Date(billing.subscription.ends_at).toLocaleDateString(),
                        })}
                    </p>
                ) : null}
            </CardContent>

            {billing.subscription.subscribed ? (
                <CardFooter className="flex flex-wrap gap-2">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => router.visit(BillingController.updatePaymentMethod.url())}
                    >
                        {t('billing.update_payment_method')}
                    </Button>

                    {billing.subscription.on_grace_period ? (
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => router.post(BillingController.resume.url(), {}, { preserveScroll: true })}
                        >
                            {t('billing.resume_subscription')}
                        </Button>
                    ) : (
                        <Button
                            type="button"
                            variant="destructive"
                            onClick={() => router.post(BillingController.cancel.url(), {}, { preserveScroll: true })}
                        >
                            {t('billing.cancel_subscription')}
                        </Button>
                    )}
                </CardFooter>
            ) : null}
        </Card>
    );
}

function PlanCard({
    planKey,
    plan,
    billing,
    onCheckoutError,
}: {
    planKey: string;
    plan: BillingPlan;
    billing: BillingProps;
    onCheckoutError: (message: string | null) => void;
}) {
    const { t } = useTranslation();

    return (
        <Card className="flex h-full flex-col">
            <CardHeader>
                <CardTitle className="flex items-center justify-between gap-3">
                    <span>{plan.name}</span>
                    {billing.subscription.current_price_id &&
                    Object.values(plan.prices).includes(billing.subscription.current_price_id) ? (
                        <Badge>{t('billing.current_plan')}</Badge>
                    ) : null}
                </CardTitle>
                <CardDescription>{plan.description}</CardDescription>
            </CardHeader>

            <CardContent className="space-y-4">
                <div className="rounded-lg border border-border/70 bg-muted/30 p-3">
                    <div className="text-sm font-medium">{PLAN_PRICES[planKey as keyof typeof PLAN_PRICES].monthly}</div>
                    <div className="text-sm text-muted-foreground">{PLAN_PRICES[planKey as keyof typeof PLAN_PRICES].yearly}</div>
                </div>
            </CardContent>

            <CardFooter className="mt-auto flex flex-col gap-2">
                <IntervalButton
                    billing={billing}
                    interval="monthly"
                    plan={plan}
                    planKey={planKey}
                    onCheckoutError={onCheckoutError}
                />
                <IntervalButton
                    billing={billing}
                    interval="yearly"
                    plan={plan}
                    planKey={planKey}
                    onCheckoutError={onCheckoutError}
                />
            </CardFooter>
        </Card>
    );
}

function IntervalButton({
    billing,
    interval,
    plan,
    planKey,
    onCheckoutError,
}: {
    billing: BillingProps;
    interval: 'monthly' | 'yearly';
    plan: BillingPlan;
    planKey: string;
    onCheckoutError: (message: string | null) => void;
}) {
    const { t } = useTranslation();
    const priceId = plan.prices[interval];
    const isCurrentPlan = billing.subscription.current_price_id === priceId;

    const handleClick = () => {
        if (billing.subscription.subscribed) {
            onCheckoutError(null);
            router.post(
                BillingController.swap.url(),
                { plan: planKey, interval },
                { preserveScroll: true },
            );

            return;
        }

        void requestCheckoutOptions(planKey, interval, onCheckoutError);
    };

    return (
        <Button
            type="button"
            className="w-full"
            variant={isCurrentPlan ? 'outline' : 'default'}
            disabled={isCurrentPlan}
            onClick={handleClick}
        >
            {billing.subscription.subscribed
                ? t(`billing.switch_${interval}`)
                : t(`billing.start_${interval}`)}
        </Button>
    );
}

async function requestCheckoutOptions(
    plan: string,
    interval: 'monthly' | 'yearly',
    onCheckoutError: (message: string | null) => void,
): Promise<void> {
    onCheckoutError(null);

    const response = await fetch(
        BillingController.checkout.url({
            query: { plan, interval },
        }),
        {
            headers: {
                Accept: 'application/json',
            },
            method: 'GET',
            credentials: 'same-origin',
        },
    );

    if (!response.ok) {
        onCheckoutError('Unable to start Paddle checkout right now.');

        return;
    }

    const checkout = (await response.json()) as CheckoutOptions;

    if (!window.Paddle) {
        onCheckoutError('Paddle checkout is not available in the browser.');

        return;
    }

    window.Paddle.Checkout.open({
        ...checkout,
        settings: {
            ...checkout.settings,
            displayMode: 'overlay',
        },
        eventCallback: (event: { name: string; error?: { type?: string; detail?: string; code?: string } }) => {
            if (event.name === 'checkout.error') {
                onCheckoutError(
                    `Paddle checkout error: ${event.error?.code ?? event.error?.type ?? 'unknown'} — ${event.error?.detail ?? 'check Paddle dashboard configuration'}`,
                );
            }
        },
    });
}
