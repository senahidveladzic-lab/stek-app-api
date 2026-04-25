import { Head, router, useForm, usePage } from '@inertiajs/react';
import { startOfMonth, endOfMonth } from 'date-fns';
import { CalendarRange, Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { DateRangePicker } from '@/components/date-range-picker';
import { MonthSelector } from '@/components/month-selector';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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

const CURRENCY_OPTIONS = ['BAM', 'EUR', 'USD', 'GBP', 'CHF', 'RSD', 'HRK'];

function toDateString(date: Date): string {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

type Category = {
    id: number;
    name: string;
    icon: string;
    color: string;
};

type Expense = {
    id: number;
    amount: string;
    currency: string;
    original_amount: string | null;
    original_currency: string | null;
    description: string | null;
    original_text: string | null;
    expense_date: string;
    category: Category | null;
    user: { id: number; name: string } | null;
};

type PaginatedExpenses = {
    data: Expense[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

type Filters = {
    category_id?: string;
    from?: string;
    to?: string;
    search?: string;
};

type PageProps = {
    expenses: PaginatedExpenses;
    categories: Category[];
    filters: Filters;
    month_total: number;
    year_total: number;
};

export default function ExpensesIndex() {
    const { t, formatMoney, formatDate } = useTranslation();
    const { expenses, categories, filters, month_total, year_total } = usePage<PageProps>().props;
    const [showAddModal, setShowAddModal] = useState(false);
    const [editingExpense, setEditingExpense] = useState<Expense | null>(null);
    const [showVoiceTab, setShowVoiceTab] = useState(true);
    const [deleteConfirm, setDeleteConfirm] = useState<number | null>(null);
    const [useCustomRange, setUseCustomRange] = useState(false);

    useBreadcrumbs([{ title: t('nav.expenses'), href: '/expenses' }]);

    function handleFilter(key: string, value: string) {
        router.get(
            '/expenses',
            { ...filters, [key]: value || undefined },
            { preserveState: true, preserveScroll: true },
        );
    }

    function handleDelete(id: number) {
        router.delete(`/expenses/${id}`, {
            preserveScroll: true,
            onSuccess: () => setDeleteConfirm(null),
        });
    }

    return (
        <>
            <Head title={t('expenses.title')} />
            <div className="flex flex-1 flex-col gap-4 p-4">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <h1 className="text-2xl font-bold">{t('expenses.title')}</h1>
                    <Button onClick={() => { setShowAddModal(true); setShowVoiceTab(true); }}>
                        <Plus className="mr-2 h-4 w-4" />
                        {t('expenses.add')}
                    </Button>
                </div>

                {/* Filters */}
                <Card>
                    <CardContent className="pt-6">
                        <div className="flex flex-wrap gap-3">
                            <div className="relative flex-1">
                                <Search className="text-muted-foreground absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2" />
                                <Input
                                    placeholder={t('common.search')}
                                    className="pl-9"
                                    defaultValue={filters.search ?? ''}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter') {
                                            handleFilter('search', e.currentTarget.value);
                                        }
                                    }}
                                />
                            </div>
                            <Select
                                value={filters.category_id ?? 'all'}
                                onValueChange={(v) => handleFilter('category_id', v === 'all' ? '' : v)}
                            >
                                <SelectTrigger className="w-[180px]">
                                    <SelectValue placeholder={t('expenses.filter_by_category')} />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">{t('expenses.all_categories')}</SelectItem>
                                    {categories.map((cat) => (
                                        <SelectItem key={cat.id} value={String(cat.id)}>
                                            {cat.icon} {t('categories.' + cat.name)}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {useCustomRange ? (
                                <DateRangePicker
                                    from={filters.from}
                                    to={filters.to}
                                    onChange={(from, to) => {
                                        router.get(
                                            '/expenses',
                                            { ...filters, from: from || undefined, to: to || undefined },
                                            { preserveState: true, preserveScroll: true },
                                        );
                                    }}
                                />
                            ) : (
                                <MonthSelector
                                    from={filters.from}
                                    to={filters.to}
                                    onChange={(from, to) => {
                                        router.get(
                                            '/expenses',
                                            { ...filters, from, to },
                                            { preserveState: true, preserveScroll: true },
                                        );
                                    }}
                                />
                            )}
                            <Button
                                variant={useCustomRange ? 'default' : 'outline'}
                                size="icon"
                                className="h-9 w-9"
                                title={t('expenses.custom_range')}
                                onClick={() => {
                                    if (useCustomRange) {
                                        const now = new Date();
                                        const from = toDateString(startOfMonth(now));
                                        const to = toDateString(endOfMonth(now));
                                        router.get(
                                            '/expenses',
                                            { ...filters, from, to },
                                            { preserveState: true, preserveScroll: true },
                                        );
                                    }
                                    setUseCustomRange(!useCustomRange);
                                }}
                            >
                                <CalendarRange className="h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* Totals */}
                <div className="grid grid-cols-2 gap-4">
                    <Card>
                        <CardContent className="flex items-center justify-between py-4">
                            <span className="text-muted-foreground text-sm font-medium">{t('expenses.month_total')}</span>
                            <span className="text-lg font-bold">{formatMoney(month_total)}</span>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent className="flex items-center justify-between py-4">
                            <span className="text-muted-foreground text-sm font-medium">{t('expenses.year_total')}</span>
                            <span className="text-lg font-bold">{formatMoney(year_total)}</span>
                        </CardContent>
                    </Card>
                </div>

                {/* Table */}
                <Card>
                    <CardContent className="p-0">
                        {expenses.data.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b">
                                            <th className="px-4 py-3 text-left font-medium">{t('expenses.date')}</th>
                                            <th className="px-4 py-3 text-left font-medium">{t('expenses.category')}</th>
                                            <th className="px-4 py-3 text-left font-medium">{t('expenses.description')}</th>
                                            <th className="px-4 py-3 text-right font-medium">{t('expenses.amount')}</th>
                                            <th className="px-4 py-3 text-left font-medium">{t('expenses.added_by')}</th>
                                            <th className="px-4 py-3 text-right font-medium"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {expenses.data.map((expense) => (
                                            <tr key={expense.id} className="border-b last:border-0">
                                                <td className="px-4 py-3">{formatDate(expense.expense_date)}</td>
                                                <td className="px-4 py-3">
                                                    {expense.category && (
                                                        <span
                                                            className="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs"
                                                            style={{ backgroundColor: expense.category.color + '20', color: expense.category.color }}
                                                        >
                                                            {expense.category.icon} {t('categories.' + expense.category.name)}
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3">{expense.description ?? '-'}</td>
                                                <td className="px-4 py-3 text-right font-medium">
                                                    <span>{formatMoney(parseFloat(expense.amount))}</span>
                                                    {expense.original_amount && expense.original_currency && (
                                                        <span className="text-muted-foreground ml-2 inline-flex items-center rounded-full border px-1.5 py-0.5 text-[10px] font-normal">
                                                            {parseFloat(expense.original_amount).toFixed(2)} {expense.original_currency}
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="text-muted-foreground px-4 py-3 text-xs">
                                                    {expense.user?.name ?? '-'}
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <div className="flex justify-end gap-1">
                                                        <Button
                                                            variant="ghost"
                                                            size="icon"
                                                            onClick={() => setEditingExpense(expense)}
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Button>
                                                        {deleteConfirm === expense.id ? (
                                                            <Button
                                                                variant="destructive"
                                                                size="sm"
                                                                onClick={() => handleDelete(expense.id)}
                                                            >
                                                                {t('common.confirm')}
                                                            </Button>
                                                        ) : (
                                                            <Button
                                                                variant="ghost"
                                                                size="icon"
                                                                onClick={() => setDeleteConfirm(expense.id)}
                                                            >
                                                                <Trash2 className="h-4 w-4" />
                                                            </Button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <p className="text-muted-foreground py-12 text-center text-sm">
                                {t('expenses.no_expenses')}
                            </p>
                        )}
                    </CardContent>
                </Card>

                {/* Pagination */}
                {expenses.last_page > 1 && (
                    <div className="flex justify-center gap-1">
                        {expenses.links.map((link, i) => (
                            <Button
                                key={i}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url, {}, { preserveState: true })}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}

                {/* Add Modal */}
                <AddExpenseModal
                    open={showAddModal}
                    onClose={() => setShowAddModal(false)}
                    categories={categories}
                    showVoiceTab={showVoiceTab}
                    onTabChange={setShowVoiceTab}
                />

                {/* Edit Modal */}
                {editingExpense && (
                    <EditExpenseModal
                        expense={editingExpense}
                        onClose={() => setEditingExpense(null)}
                        categories={categories}
                    />
                )}
            </div>
        </>
    );
}

ExpensesIndex.layout = appLayout;

function AddExpenseModal({
    open,
    onClose,
    categories,
    showVoiceTab,
    onTabChange,
}: {
    open: boolean;
    onClose: () => void;
    categories: Category[];
    showVoiceTab: boolean;
    onTabChange: (v: boolean) => void;
}) {
    const { t } = useTranslation();
    const { household } = usePage().props;
    const [voiceText, setVoiceText] = useState('');
    const [aiResult, setAiResult] = useState<Record<string, unknown> | null>(null);
    const [aiLoading, setAiLoading] = useState(false);
    const [aiError, setAiError] = useState('');

    const form = useForm({
        amount: '',
        currency: household?.default_currency ?? 'BAM',
        category_id: '',
        description: '',
        expense_date: new Date().toISOString().slice(0, 10),
    });

    function handleVoiceParse() {
        setAiLoading(true);
        setAiError('');
        fetch('/expenses/voice', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] ?? ''),
                Accept: 'application/json',
            },
            body: JSON.stringify({ text: voiceText }),
        })
            .then((res) => res.json())
            .then((json) => {
                if (json.data) {
                    setAiResult(json.data);
                    form.setData({
                        amount: String(json.data.amount ?? ''),
                        currency: json.data.currency ?? household?.default_currency ?? 'BAM',
                        category_id: String(json.data.category_id ?? ''),
                        description: json.data.description ?? '',
                        expense_date: json.data.date ?? new Date().toISOString().slice(0, 10),
                    });
                } else {
                    setAiError(json.message ?? t('errors.ai_parse_failed'));
                }
            })
            .catch(() => setAiError(t('errors.generic')))
            .finally(() => setAiLoading(false));
    }

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.post('/expenses', {
            preserveScroll: true,
            onSuccess: () => {
                onClose();
                form.reset();
                setVoiceText('');
                setAiResult(null);
            },
        });
    }

    return (
        <Dialog open={open} onOpenChange={(v) => !v && onClose()}>
            <DialogContent className="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('expenses.add')}</DialogTitle>
                </DialogHeader>

                <div className="mb-4 flex gap-2">
                    <Button
                        variant={showVoiceTab ? 'default' : 'outline'}
                        size="sm"
                        onClick={() => onTabChange(true)}
                    >
                        {t('expenses.voice_input')}
                    </Button>
                    <Button
                        variant={!showVoiceTab ? 'default' : 'outline'}
                        size="sm"
                        onClick={() => onTabChange(false)}
                    >
                        {t('expenses.manual_input')}
                    </Button>
                </div>

                {showVoiceTab && !aiResult && (
                    <div className="space-y-3">
                        <textarea
                            className="border-input bg-background w-full rounded-md border px-3 py-2 text-sm"
                            rows={3}
                            placeholder={t('expenses.voice_input_placeholder')}
                            value={voiceText}
                            onChange={(e) => setVoiceText(e.target.value)}
                        />
                        {aiError && <p className="text-destructive text-sm">{aiError}</p>}
                        <Button onClick={handleVoiceParse} disabled={!voiceText.trim() || aiLoading}>
                            {aiLoading ? t('common.loading') : t('common.confirm')}
                        </Button>
                    </div>
                )}

                {(showVoiceTab && aiResult) || !showVoiceTab ? (
                    <div>
                        {aiResult && (
                            <p className="text-muted-foreground mb-3 text-sm">{t('expenses.ai_parsed')}</p>
                        )}
                        <ExpenseForm form={form} categories={categories} onSubmit={handleSubmit} />
                    </div>
                ) : null}
            </DialogContent>
        </Dialog>
    );
}

function EditExpenseModal({
    expense,
    onClose,
    categories,
}: {
    expense: Expense;
    onClose: () => void;
    categories: Category[];
}) {
    const { t } = useTranslation();

    const form = useForm({
        amount: expense.original_amount ?? expense.amount,
        currency: expense.original_currency ?? expense.currency,
        category_id: String(expense.category?.id ?? ''),
        description: expense.description ?? '',
        expense_date: expense.expense_date,
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.put(`/expenses/${expense.id}`, {
            preserveScroll: true,
            onSuccess: () => onClose(),
        });
    }

    return (
        <Dialog open onOpenChange={(v) => !v && onClose()}>
            <DialogContent className="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{t('expenses.edit')}</DialogTitle>
                </DialogHeader>
                <ExpenseForm form={form} categories={categories} onSubmit={handleSubmit} />
            </DialogContent>
        </Dialog>
    );
}

function ExpenseForm({
    form,
    categories,
    onSubmit,
}: {
    form: ReturnType<typeof useForm<{
        amount: string;
        currency: string;
        category_id: string;
        description: string;
        expense_date: string;
    }>>;
    categories: Category[];
    onSubmit: (e: React.FormEvent) => void;
}) {
    const { t } = useTranslation();

    return (
        <form onSubmit={onSubmit} className="space-y-4">
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <Label>{t('expenses.amount')}</Label>
                    <div className="flex gap-2">
                        <Input
                            type="number"
                            step="0.01"
                            className="flex-1"
                            value={form.data.amount}
                            onChange={(e) => form.setData('amount', e.target.value)}
                        />
                        <Select
                            value={form.data.currency}
                            onValueChange={(v) => form.setData('currency', v)}
                        >
                            <SelectTrigger className="w-[90px]">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {CURRENCY_OPTIONS.map((c) => (
                                    <SelectItem key={c} value={c}>{c}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                    {form.errors.amount && (
                        <p className="text-destructive mt-1 text-xs">{form.errors.amount}</p>
                    )}
                </div>
                <div>
                    <Label>{t('expenses.category')}</Label>
                    <Select
                        value={form.data.category_id}
                        onValueChange={(v) => form.setData('category_id', v)}
                    >
                        <SelectTrigger>
                            <SelectValue placeholder={t('expenses.category')} />
                        </SelectTrigger>
                        <SelectContent>
                            {categories.map((cat) => (
                                <SelectItem key={cat.id} value={String(cat.id)}>
                                    {cat.icon} {t('categories.' + cat.name)}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    {form.errors.category_id && (
                        <p className="text-destructive mt-1 text-xs">{form.errors.category_id}</p>
                    )}
                </div>
            </div>
            <div>
                <Label>{t('expenses.description')}</Label>
                <Input
                    value={form.data.description}
                    onChange={(e) => form.setData('description', e.target.value)}
                />
            </div>
            <div>
                <Label>{t('expenses.date')}</Label>
                <Input
                    type="date"
                    value={form.data.expense_date}
                    onChange={(e) => form.setData('expense_date', e.target.value)}
                />
            </div>
            <div className="flex justify-end gap-2">
                <Button type="submit" disabled={form.processing}>
                    {t('common.save')}
                </Button>
            </div>
        </form>
    );
}
