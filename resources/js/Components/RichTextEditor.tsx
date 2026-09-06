import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import TextAlign from '@tiptap/extension-text-align';
import { useEffect, useState } from 'react';
import { Button, Space, Tooltip } from 'antd';
import {
    BoldOutlined, ItalicOutlined, StrikethroughOutlined, OrderedListOutlined,
    UnorderedListOutlined, LinkOutlined, PictureOutlined, UndoOutlined, RedoOutlined,
} from '@ant-design/icons';
import MediaPicker from './MediaPicker';

interface Props {
    value: string;
    onChange: (html: string) => void;
    minHeight?: number;
}

/** TipTap-based HTML editor. Output is sanitised server-side (mews/purifier). */
export default function RichTextEditor({ value, onChange, minHeight = 220 }: Props) {
    const [pickImage, setPickImage] = useState(false);

    const editor = useEditor({
        extensions: [
            StarterKit.configure({ heading: { levels: [2, 3, 4] } }),
            Link.configure({ openOnClick: false, autolink: true }),
            Image.configure({ inline: false }),
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
        ],
        content: value || '',
        onUpdate: ({ editor }) => onChange(editor.getHTML()),
        editorProps: {
            attributes: {
                class: 'rte-content',
                style: `min-height:${minHeight}px;outline:none;padding:12px;`,
                dir: 'rtl',
            },
        },
    });

    // keep editor in sync when the form resets / loads a record
    useEffect(() => {
        if (editor && value !== editor.getHTML()) {
            editor.commands.setContent(value || '', { emitUpdate: false });
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [value, editor]);

    if (!editor) return null;

    const btn = (active: boolean, icon: React.ReactNode, onClick: () => void, title: string) => (
        <Tooltip title={title}>
            <Button size="small" type={active ? 'primary' : 'default'} icon={icon} onClick={onClick} />
        </Tooltip>
    );

    return (
        <div style={{ border: '1px solid #d9d9d9', borderRadius: 8, overflow: 'hidden' }}>
            <Space wrap size={4} style={{ padding: 8, borderBottom: '1px solid #f0f0f0', background: '#fafafa' }}>
                {btn(editor.isActive('bold'), <BoldOutlined />, () => editor.chain().focus().toggleBold().run(), 'ضخیم')}
                {btn(editor.isActive('italic'), <ItalicOutlined />, () => editor.chain().focus().toggleItalic().run(), 'مورب')}
                {btn(editor.isActive('strike'), <StrikethroughOutlined />, () => editor.chain().focus().toggleStrike().run(), 'خط‌خورده')}
                {btn(editor.isActive('heading', { level: 2 }), <b>H2</b>, () => editor.chain().focus().toggleHeading({ level: 2 }).run(), 'سرتیتر ۲')}
                {btn(editor.isActive('heading', { level: 3 }), <b>H3</b>, () => editor.chain().focus().toggleHeading({ level: 3 }).run(), 'سرتیتر ۳')}
                {btn(editor.isActive('bulletList'), <UnorderedListOutlined />, () => editor.chain().focus().toggleBulletList().run(), 'فهرست')}
                {btn(editor.isActive('orderedList'), <OrderedListOutlined />, () => editor.chain().focus().toggleOrderedList().run(), 'فهرست شماره‌دار')}
                {btn(editor.isActive('link'), <LinkOutlined />, () => {
                    const url = window.prompt('نشانی لینک:', editor.getAttributes('link').href ?? 'https://');
                    if (url === null) return;
                    if (url === '') editor.chain().focus().unsetLink().run();
                    else editor.chain().focus().setLink({ href: url }).run();
                }, 'لینک')}
                {btn(false, <PictureOutlined />, () => setPickImage(true), 'تصویر')}
                {btn(false, <UndoOutlined />, () => editor.chain().focus().undo().run(), 'واگرد')}
                {btn(false, <RedoOutlined />, () => editor.chain().focus().redo().run(), 'ازنو')}
            </Space>

            <EditorContent editor={editor} />

            <MediaPicker
                open={pickImage}
                onClose={() => setPickImage(false)}
                onSelect={(m) => {
                    const media = Array.isArray(m) ? m[0] : m;
                    editor.chain().focus().setImage({ src: media.url, alt: media.alt_text ?? '' }).run();
                }}
            />
        </div>
    );
}
