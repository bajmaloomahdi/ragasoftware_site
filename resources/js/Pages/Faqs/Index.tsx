import { Tag } from 'antd';
import { QuestionCircleOutlined } from '@ant-design/icons';
import CrudPage from '@/Components/CrudPage';
import type { PageProps } from '@/types/global';
import type { Paginated } from '@/types/models';

interface Faq {
    id: number;
    category_id: number | null;
    category: { name: string } | null;
    question: string;
    answer: string;
    is_active: boolean;
    sort_order: number;
}

type Props = PageProps<{
    rows: Paginated<Faq>;
    filters: Record<string, string | undefined>;
    categories: { id: number; name: string }[];
}>;

export default function FaqsIndex({ rows, filters, categories }: Props) {
    return (
        <CrudPage<Faq>
            title="سؤالات متداول"
            icon={<QuestionCircleOutlined />}
            routeKey="faqs"
            rows={rows}
            filters={filters}
            width={560}
            searchPlaceholder="جستجو در پرسش و پاسخ"
            filterDefs={[
                {
                    key: 'category',
                    placeholder: 'دسته',
                    options: categories.map((c) => ({ label: c.name, value: String(c.id) })),
                },
            ]}
            columns={[
                { title: 'پرسش', dataIndex: 'question' },
                { title: 'دسته', dataIndex: ['category', 'name'], render: (v) => v || '—' },
                { title: 'وضعیت', dataIndex: 'is_active', render: (v: boolean) => <Tag color={v ? 'green' : 'default'}>{v ? 'فعال' : 'غیرفعال'}</Tag> },
            ]}
            fields={[
                { key: 'question', type: 'text', label: 'پرسش', required: true },
                { key: 'answer', type: 'richtext', label: 'پاسخ', required: true },
                {
                    key: 'category_id',
                    type: 'select',
                    label: 'دسته',
                    options: categories.map((c) => ({ label: c.name, value: c.id })),
                },
                { key: 'is_active', type: 'switch', label: 'فعال' },
                { key: 'sort_order', type: 'number', label: 'ترتیب' },
            ]}
            toForm={(r) => ({
                question: r?.question ?? '',
                answer: r?.answer ?? '',
                category_id: r?.category_id ?? null,
                is_active: r?.is_active ?? true,
                sort_order: r?.sort_order ?? 0,
            })}
        />
    );
}
