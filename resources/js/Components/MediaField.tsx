import { useState } from 'react';
import { Button, Image, Space, Typography } from 'antd';
import { PictureOutlined, DeleteOutlined } from '@ant-design/icons';
import MediaPicker from './MediaPicker';
import type { MediaItem } from '@/types/models';

interface Props {
    label?: string;
    /** Currently selected media id */
    value: number | null;
    /** Optional preview url when the parent already knows it */
    previewUrl?: string | null;
    onChange: (id: number | null, media?: MediaItem) => void;
}

/** Single-image picker field used throughout the admin forms. */
export default function MediaField({ label, value, previewUrl, onChange }: Props) {
    const [open, setOpen] = useState(false);
    const [localPreview, setLocalPreview] = useState<string | null>(previewUrl ?? null);

    return (
        <div>
            {label && (
                <Typography.Text style={{ display: 'block', marginBottom: 6 }}>{label}</Typography.Text>
            )}
            <Space align="start">
                <div
                    style={{
                        width: 96,
                        height: 96,
                        borderRadius: 8,
                        border: '1px dashed #d0d5dd',
                        display: 'grid',
                        placeItems: 'center',
                        overflow: 'hidden',
                        background: '#f5f7fa',
                    }}
                >
                    {value && localPreview ? (
                        <Image src={localPreview} width={96} height={96} style={{ objectFit: 'cover' }} preview={false} />
                    ) : (
                        <PictureOutlined style={{ fontSize: 22, color: '#98a2b3' }} />
                    )}
                </div>
                <Space direction="vertical">
                    <Button icon={<PictureOutlined />} onClick={() => setOpen(true)}>
                        {value ? 'تغییر تصویر' : 'انتخاب تصویر'}
                    </Button>
                    {value && (
                        <Button
                            danger
                            type="text"
                            icon={<DeleteOutlined />}
                            onClick={() => {
                                setLocalPreview(null);
                                onChange(null);
                            }}
                        >
                            حذف
                        </Button>
                    )}
                </Space>
            </Space>

            <MediaPicker
                open={open}
                onClose={() => setOpen(false)}
                onSelect={(m) => {
                    const media = Array.isArray(m) ? m[0] : m;
                    setLocalPreview(media.thumb_url);
                    onChange(media.id, media);
                }}
            />
        </div>
    );
}
