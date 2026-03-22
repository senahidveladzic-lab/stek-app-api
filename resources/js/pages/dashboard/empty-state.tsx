import { Link } from '@inertiajs/react';
import { PlusCircle, ReceiptText } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useTranslation } from '@/hooks/use-translation';

export function EmptyState() {
    const { t } = useTranslation();

    return (
        <div className="flex flex-col items-center justify-center py-16 text-center">
            <div className="bg-muted mb-4 flex h-16 w-16 items-center justify-center rounded-full">
                <ReceiptText className="text-muted-foreground h-8 w-8" />
            </div>
            <h3 className="mb-1 text-lg font-semibold">{t('dashboard.empty_title')}</h3>
            <p className="text-muted-foreground mb-6 max-w-sm text-sm">
                {t('dashboard.empty_description')}
            </p>
            <Button asChild>
                <Link href="/expenses">
                    <PlusCircle className="h-4 w-4" />
                    {t('expenses.add')}
                </Link>
            </Button>
        </div>
    );
}
