export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

export interface MediaItem {
    id: number;
    folder_id: number | null;
    disk: string;
    path: string;
    filename: string;
    original_name: string;
    mime_type: string;
    extension: string;
    size: number;
    width: number | null;
    height: number | null;
    alt_text: string | null;
    title: string | null;
    caption: string | null;
    variants: Record<string, unknown> | null;
    url: string;
    thumb_url: string;
    created_at: string;
}

export interface MediaFolderItem {
    id: number;
    parent_id: number | null;
    name: string;
    slug: string;
    path: string;
}

export type ContentStatus = 'draft' | 'scheduled' | 'published';

export interface SeoMetaForm {
    meta_title: string | null;
    meta_description: string | null;
    focus_keyword: string | null;
    canonical_url: string | null;
    meta_robots: string | null;
    no_index: boolean;
    og_title: string | null;
    og_description: string | null;
    og_media_id: number | null;
    twitter_card: string | null;
    twitter_title: string | null;
    twitter_description: string | null;
    twitter_media_id: number | null;
    schema_type: string | null;
}
