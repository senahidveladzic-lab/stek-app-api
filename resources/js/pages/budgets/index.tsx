import { Head, router, useForm, usePage } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

type Category = {
    id: number;
    name: string;
    icon: string;
    color: string;
};

type CategoryBudget = {
    id: number;
    category_id: number;
    category_name: string;
    category_icon: string;
    category_color: string;
    amount: number;
};

type PageProps = {
    month: string;
    overall_budget: number | null;
    category_budgets: CategoryBudget[];
    categories: Category[];
    currency: string;
};

function getMonthLabel(monthStr: string, locale: string): string {
    const date = new Date(monthStr + 'T00:00:00');
    return date.toLocaleDateString(locale === 'bs' ? 'bs-BA' : 'en-US', {
        month: 'long',
        year: 'numeric',
    });
}

function shiftMonth(monthStr: string, delta: number): string {
    const date = new Date(monthStr + 'T00:00:00');
    date.setMonth(date.getMonth() + delta);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    return `${y}-${m}-01`;
}

export default function BudgetIndex() {
    const { t, formatMoney, locale } = useTranslation();
    const { month, overall_budget, category_budgets, categories, currency } =
        usePage<PageProps>().props;

    const [addingCategory, setAddingCategory] = useState(false);
    const [newCategoryId, setNewCategoryId] = useState<string>('');

    const { data, setData, post, processing } = useForm({
        month,
        overall_amount: overall_budget ?? ('' as number | ''),
        categories: category_budgets.map((cb) => ({
            category_id: cb.category_id,
            amount: cb.amount,
        })),
    });

    const usedCategoryIds = data.categories.map((c) => c.category_id);
    const availableCategories = categories.filter((c) => !usedCategoryIds.includes(c.id));

    useBreadcrumbs([{ title: t('nav.budgets'), href: '/budgets' }]);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        post('/budgets', { preserveScroll: true });
    }

    function addCategoryBudget() {
        if (!newCategoryId) return;
        const catId = parseInt(newCategoryId, 10);
        setData('categories', [...data.categories, { category_id: catId, amount: 0 }]);
        setNewCategoryId('');
        setAddingCategory(false);
    }

    function removeCategoryBudget(categoryId: number) {
        setData('categories', data.categories.filter((c) => c.category_id !== categoryId));
    }

    function updateCategoryAmount(categoryId: number, amount: number) {
        setData(
            'categories',
            data.categories.map((c) =>
                c.category_id === categoryId ? { ...c, amount } : c,
            ),
        );
    }

    function getCategoryInfo(categoryId: number) {
        const fromBudgets = category_budgets.find((cb) => cb.category_id === categoryId);
        if (fromBudgets) {
            return { name: fromBudgets.category_name, icon: fromBudgets.category_icon };
        }
        const fromList = categories.find((c) => c.id === categoryId);
        return fromList ? { name: fromList.name, icon: fromList.icon } : { name: '', icon: '' };
    }

    const allocatedTotal = data.categories.reduce((sum, c) => sum + (Number(c.amount) || 0), 0);
    const overallNum = Number(data.overall_amount) || 0;
    const unallocated = overallNum > 0 ? overallNum - allocatedTotal : null;

    return (
        <>
            <Head title={t('budget.title')} />
            <div className="flex flex-1 flex-col gap-6 p-4 md:p-6">
                <div className="flex items-center justify-center gap-4">
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => router.get('/budgets', { month: shiftMonth(month, -1) })}
                    >
                        <ChevronLeft className="h-5 w-5" />
                    </Button>
                    <h2 className="min-w-[180px] text-center text-xl font-bold capitalize">
                        {getMonthLabel(month, locale)}
                    </h2>
                    <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => router.get('/budgets', { month: shiftMonth(month, 1) })}
                    >
                        <ChevronRight className="h-5 w-5" />
                    </Button>
                </div>

                <form onSubmit={handleSubmit} className="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{t('budget.overall')}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center gap-3">
                                <Label htmlFor="overall" className="shrink-0 font-medium">
                                    {t('budget.monthly_limit')}
                                </Label>
                                <Input
                                    id="overall"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    className="max-w-[200px] tabular-nums"
                                    placeholder="0.00"
                                    value={data.overall_amount}
                                    onChange={(e) =>
                                        setData('overall_amount', e.target.value === '' ? '' : parseFloat(e.target.value))
                                    }
                                />
                                <span className="text-muted-foreground text-sm">{currency}</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex-row items-center justify-between">
                            <CardTitle>{t('budget.by_category')}</CardTitle>
                            {unallocated !== null && (
                                <span className={`text-sm font-medium tabular-nums ${unallocated < 0 ? 'text-red-500' : 'text-muted-foreground'}`}>
                                    {t('budget.unallocated')}: {formatMoney(unallocated)}
                                </span>
                            )}
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {data.categories.map((cat) => {
                                const info = getCategoryInfo(cat.category_id);
                                return (
                                    <div key={cat.category_id} className="flex items-center gap-3">
                                        <span className="w-8 text-center text-lg">{info.icon}</span>
                                        <span className="min-w-[120px] text-sm font-medium">
                                            {t('categories.' + info.name)}
                                        </span>
                                        <Input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            className="max-w-[160px] tabular-nums"
                                            value={cat.amount}
                                            onChange={(e) =>
                                                updateCategoryAmount(cat.category_id, parseFloat(e.target.value) || 0)
                                            }
                                        />
                                        <span className="text-muted-foreground text-sm">{currency}</span>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            className="text-muted-foreground hover:text-destructive h-8 w-8"
                                            onClick={() => removeCategoryBudget(cat.category_id)}
                                        >
                                            <Trash2 className="h-4 w-4" />
                                        </Button>
                                    </div>
                                );
                            })}

                            {addingCategory ? (
                                <div className="flex items-center gap-3">
                                    <Select value={newCategoryId} onValueChange={setNewCategoryId}>
                                        <SelectTrigger className="max-w-[220px]">
                                            <SelectValue placeholder={t('budget.select_category')} />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {availableCategories.map((cat) => (
                                                <SelectItem key={cat.id} value={String(cat.id)}>
                                                    {cat.icon} {t('categories.' + cat.name)}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    <Button type="button" size="sm" onClick={addCategoryBudget} disabled={!newCategoryId}>
                                        {t('common.confirm')}
                                    </Button>
                                    <Button type="button" variant="ghost" size="sm" onClick={() => setAddingCategory(false)}>
                                        {t('common.cancel')}
                                    </Button>
                                </div>
                            ) : availableCategories.length > 0 ? (
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    className="gap-1"
                                    onClick={() => setAddingCategory(true)}
                                >
                                    <Plus className="h-4 w-4" />
                                    {t('budget.add_category')}
                                </Button>
                            ) : null}
                        </CardContent>
                    </Card>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={processing}>
                            {t('common.save')}
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

BudgetIndex.layout = appLayout;
