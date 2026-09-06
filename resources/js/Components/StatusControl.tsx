import { DatePicker, Form, Segmented } from 'antd';
import dayjs, { Dayjs } from 'dayjs';
import type { ContentStatus } from '@/types/models';

interface Props {
    status: ContentStatus;
    publishedAt: string | null;
    onChange: (patch: { status: ContentStatus; published_at: string | null }) => void;
}

const OPTIONS = [
    { label: 'پیش‌نویس', value: 'draft' },
    { label: 'زمان‌بندی', value: 'scheduled' },
    { label: 'انتشار', value: 'published' },
];

/** Draft / Scheduled / Published + publish datetime, used on every publishable form. */
export default function StatusControl({ status, publishedAt, onChange }: Props) {
    const value: Dayjs | null = publishedAt ? dayjs(publishedAt) : null;

    return (
        <div>
            <Form.Item label="وضعیت انتشار" style={{ marginBottom: 12 }}>
                <Segmented
                    options={OPTIONS}
                    value={status}
                    onChange={(v) => {
                        const s = v as ContentStatus;
                        onChange({
                            status: s,
                            published_at:
                                s === 'published' && !publishedAt
                                    ? dayjs().format('YYYY-MM-DD HH:mm:ss')
                                    : publishedAt,
                        });
                    }}
                />
            </Form.Item>

            {(status === 'scheduled' || status === 'published') && (
                <Form.Item label={status === 'scheduled' ? 'زمان انتشار' : 'تاریخ انتشار'}>
                    <DatePicker
                        showTime
                        style={{ width: '100%' }}
                        value={value}
                        onChange={(d) =>
                            onChange({ status, published_at: d ? d.format('YYYY-MM-DD HH:mm:ss') : null })
                        }
                    />
                </Form.Item>
            )}
        </div>
    );
}
