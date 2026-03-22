import { Head, router, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useBreadcrumbs } from '@/hooks/use-breadcrumbs';
import { useTranslation } from '@/hooks/use-translation';
import appLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';

const CURRENCY_OPTIONS = ['BAM', 'EUR', 'USD', 'GBP', 'CHF', 'RSD', 'HRK'];

type Member = {
    id: number;
    name: string;
    email: string;
};

type Invitation = {
    id: number;
    email: string;
    created_at: string;
};

type HouseholdData = {
    id: number;
    name: string;
    default_currency: string;
    owner_id: number;
    max_members: number;
    members: Member[];
    invitations: Invitation[];
};

type PageProps = {
    household: HouseholdData;
};

export default function HouseholdSettings() {
    const { t } = useTranslation();
    const { auth } = usePage().props;
    const { household } = usePage<PageProps>().props;
    const isOwner = auth.user.id === household.owner_id;

    useBreadcrumbs([{ title: t('settings.household'), href: '/household' }]);

    return (
        <>
            <Head title={t('settings.household')} />

            <h1 className="sr-only">{t('settings.household')}</h1>

            <SettingsLayout>
                <div className="space-y-6">
                    <Heading
                        variant="small"
                        title={t('settings.household')}
                        description={t('household.member_limit', {
                            count: String(household.members.length),
                            max: String(household.max_members),
                        })}
                    />

                    {isOwner ? (
                        <HouseholdForm household={household} />
                    ) : (
                        <HouseholdInfo household={household} />
                    )}

                    <MembersList
                        members={household.members}
                        ownerId={household.owner_id}
                        isOwner={isOwner}
                    />

                    {isOwner && (
                        <>
                            <InviteForm />
                            {household.invitations.length > 0 && (
                                <PendingInvitations invitations={household.invitations} />
                            )}
                        </>
                    )}
                </div>
            </SettingsLayout>
        </>
    );
}

HouseholdSettings.layout = appLayout;

function HouseholdForm({ household }: { household: HouseholdData }) {
    const { t } = useTranslation();

    const form = useForm({
        name: household.name,
        default_currency: household.default_currency,
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.patch('/household', { preserveScroll: true });
    }

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <div>
                <Label>{t('household.name')}</Label>
                <Input
                    value={form.data.name}
                    onChange={(e) => form.setData('name', e.target.value)}
                />
            </div>
            <div>
                <Label>{t('household.default_currency')}</Label>
                <Select
                    value={form.data.default_currency}
                    onValueChange={(v) => form.setData('default_currency', v)}
                >
                    <SelectTrigger className="w-[180px]">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {CURRENCY_OPTIONS.map((c) => (
                            <SelectItem key={c} value={c}>{c}</SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>
            <Button type="submit" disabled={form.processing}>
                {t('common.save')}
            </Button>
        </form>
    );
}

function HouseholdInfo({ household }: { household: HouseholdData }) {
    const { t } = useTranslation();

    return (
        <div className="space-y-2">
            <div>
                <span className="text-muted-foreground text-sm">{t('household.name')}: </span>
                <span className="font-medium">{household.name}</span>
            </div>
            <div>
                <span className="text-muted-foreground text-sm">{t('household.default_currency')}: </span>
                <span className="font-medium">{household.default_currency}</span>
            </div>
        </div>
    );
}

function MembersList({
    members,
    ownerId,
    isOwner,
}: {
    members: Member[];
    ownerId: number;
    isOwner: boolean;
}) {
    const { t } = useTranslation();
    const [removeConfirm, setRemoveConfirm] = useState<number | null>(null);

    function handleRemove(userId: number) {
        router.delete(`/household/members/${userId}`, {
            preserveScroll: true,
            onSuccess: () => setRemoveConfirm(null),
        });
    }

    return (
        <div>
            <Heading variant="small" title={t('household.members')} />
            <div className="mt-3 space-y-2">
                {members.map((member) => (
                    <Card key={member.id}>
                        <CardContent className="flex items-center justify-between py-3">
                            <div>
                                <span className="font-medium">{member.name}</span>
                                <span className="text-muted-foreground ml-2 text-sm">{member.email}</span>
                                {member.id === ownerId && (
                                    <span className="ml-2 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                                        {t('household.owner')}
                                    </span>
                                )}
                            </div>
                            {isOwner && member.id !== ownerId && (
                                <>
                                    {removeConfirm === member.id ? (
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            onClick={() => handleRemove(member.id)}
                                        >
                                            {t('common.confirm')}
                                        </Button>
                                    ) : (
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => setRemoveConfirm(member.id)}
                                        >
                                            {t('household.remove_member')}
                                        </Button>
                                    )}
                                </>
                            )}
                        </CardContent>
                    </Card>
                ))}
            </div>
        </div>
    );
}

function InviteForm() {
    const { t } = useTranslation();

    const form = useForm({
        email: '',
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.post('/household/invite', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

    return (
        <div>
            <Heading variant="small" title={t('household.invite')} />
            <form onSubmit={handleSubmit} className="mt-3 flex gap-2">
                <Input
                    type="email"
                    placeholder={t('household.invite_email_placeholder')}
                    value={form.data.email}
                    onChange={(e) => form.setData('email', e.target.value)}
                    className="flex-1"
                />
                <Button type="submit" disabled={form.processing || !form.data.email}>
                    {t('household.invite')}
                </Button>
            </form>
            {form.errors.email && (
                <p className="text-destructive mt-1 text-xs">{form.errors.email}</p>
            )}
        </div>
    );
}

function PendingInvitations({ invitations }: { invitations: Invitation[] }) {
    const { t } = useTranslation();

    return (
        <div>
            <Heading variant="small" title={t('household.pending_invitations')} />
            <div className="mt-3 space-y-2">
                {invitations.map((inv) => (
                    <Card key={inv.id}>
                        <CardContent className="flex items-center justify-between py-3">
                            <span className="text-sm">{inv.email}</span>
                            <span className="text-muted-foreground text-xs">
                                {new Date(inv.created_at).toLocaleDateString()}
                            </span>
                        </CardContent>
                    </Card>
                ))}
            </div>
        </div>
    );
}
