import { useCallback, useEffect, useRef, useState } from 'react';
import {
    Modal, Input, Upload, Button, Empty, Spin, Segmented, Image, Tooltip, App,
} from 'antd';
import { InboxOutlined, CheckCircleFilled, ReloadOutlined } from '@ant-design/icons';
import http from '@/lib/http';
import type { MediaItem, MediaFolderItem } from '@/types/models';

interface Props {
    open: boolean;
    multiple?: boolean;
    onClose: () => void;
    onSelect: (media: MediaItem | MediaItem[]) => void;
}

/**
 * Shared media browser/uploader. Every image field in the admin opens this.
 * Reads the admin media index as JSON; uploads through the same endpoint.
 */
export default function MediaPicker({ open, multiple = false, onClose, onSelect }: Props) {
    const { message } = App.useApp();
    const [loading, setLoading] = useState(false);
    const [items, setItems] = useState<MediaItem[]>([]);
    const [folders, setFolders] = useState<MediaFolderItem[]>([]);
    const [folder, setFolder] = useState<number | 0>(0);
    const [search, setSearch] = useState('');
    const [picked, setPicked] = useState<Record<number, MediaItem>>({});
    const debounce = useRef<number>();

    const load = useCallback(async () => {
        setLoading(true);
        try {
            const { data } = await http.get(route('admin.media.index'), {
                params: { json: 1, folder: folder || undefined, search: search || undefined, type: 'image' },
            });
            setItems(data.media.data ?? data.media);
            setFolders(data.folders ?? []);
        } catch {
            message.error('بارگذاری کتابخانه رسانه ناموفق بود.');
        } finally {
            setLoading(false);
        }
    }, [folder, search, message]);

    useEffect(() => {
        if (!open) return;
        window.clearTimeout(debounce.current);
        debounce.current = window.setTimeout(load, 250);
        return () => window.clearTimeout(debounce.current);
    }, [open, load]);

    useEffect(() => {
        if (!open) setPicked({});
    }, [open]);

    const toggle = (m: MediaItem) => {
        setPicked((prev) => {
            if (multiple) {
                const next = { ...prev };
                if (next[m.id]) delete next[m.id];
                else next[m.id] = m;
                return next;
            }
            return { [m.id]: m };
        });
    };

    const confirm = () => {
        const list = Object.values(picked);
        if (!list.length) return;
        onSelect(multiple ? list : list[0]);
        onClose();
    };

    const folderOptions = [
        { label: 'همه', value: 0 },
        ...folders.map((f) => ({ label: f.name, value: f.id })),
    ];

    return (
        <Modal
            open={open}
            onCancel={onClose}
            onOk={confirm}
            okText="انتخاب"
            cancelText="انصراف"
            okButtonProps={{ disabled: !Object.keys(picked).length }}
            width={860}
            title="کتابخانه رسانه"
            styles={{ body: { paddingTop: 12 } }}
        >
            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', marginBottom: 12 }}>
                <Input.Search
                    placeholder="جستجو…"
                    allowClear
                    style={{ maxWidth: 240 }}
                    onChange={(e) => setSearch(e.target.value)}
                />
                {folders.length > 0 && (
                    <Segmented options={folderOptions} value={folder} onChange={(v) => setFolder(v as number)} />
                )}
                <Button icon={<ReloadOutlined />} onClick={load} />
                <Upload
                    multiple
                    showUploadList={false}
                    customRequest={async ({ file, onSuccess, onError }) => {
                        const form = new FormData();
                        form.append('files[]', file as File);
                        if (folder) form.append('folder_id', String(folder));
                        try {
                            await http.post(route('admin.media.store'), form);
                            message.success('بارگذاری شد.');
                            onSuccess?.({});
                            load();
                        } catch (e) {
                            message.error('بارگذاری ناموفق بود.');
                            onError?.(e as Error);
                        }
                    }}
                >
                    <Button type="primary" icon={<InboxOutlined />}>
                        بارگذاری
                    </Button>
                </Upload>
            </div>

            <Spin spinning={loading}>
                {items.length === 0 && !loading ? (
                    <Empty description="رسانه‌ای یافت نشد" />
                ) : (
                    <div
                        style={{
                            display: 'grid',
                            gridTemplateColumns: 'repeat(auto-fill, minmax(120px, 1fr))',
                            gap: 10,
                            maxHeight: 420,
                            overflow: 'auto',
                        }}
                    >
                        {items.map((m) => {
                            const active = Boolean(picked[m.id]);
                            return (
                                <Tooltip key={m.id} title={m.original_name}>
                                    <div
                                        onClick={() => toggle(m)}
                                        style={{
                                            position: 'relative',
                                            border: `2px solid ${active ? '#2563eb' : 'transparent'}`,
                                            borderRadius: 8,
                                            overflow: 'hidden',
                                            cursor: 'pointer',
                                            aspectRatio: '1 / 1',
                                            background: '#f5f7fa',
                                        }}
                                    >
                                        <Image
                                            src={m.thumb_url}
                                            preview={false}
                                            width="100%"
                                            height="100%"
                                            style={{ objectFit: 'cover' }}
                                        />
                                        {active && (
                                            <CheckCircleFilled
                                                style={{
                                                    position: 'absolute',
                                                    insetInlineEnd: 6,
                                                    insetBlockStart: 6,
                                                    color: '#2563eb',
                                                    fontSize: 18,
                                                    background: '#fff',
                                                    borderRadius: '50%',
                                                }}
                                            />
                                        )}
                                    </div>
                                </Tooltip>
                            );
                        })}
                    </div>
                )}
            </Spin>
        </Modal>
    );
}
