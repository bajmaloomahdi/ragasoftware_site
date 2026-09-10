<?php

namespace App\Services\Cms;

use App\Models\Page;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

/**
 * Renders a Page's active sections, in order, each through its Blade partial
 * at resources/views/sections/{view}.blade.php.
 *
 * Each section is wrapped in an anchor container (id from config('cms.anchors'))
 * so the homepage works as a single-page, scroll-navigated experience. The id
 * is applied only to the FIRST section of each type on the page.
 */
class SectionRenderer
{
    public function render(Page $page): HtmlString
    {
        $html = '';
        $anchors = config('cms.anchors', []);
        $usedAnchors = [];

        foreach ($page->activeSections()->get() as $section) {
            $def = SectionRegistry::get($section->type);
            $view = 'sections.'.($def['view'] ?? $section->type);

            if (! View::exists($view)) {
                continue;
            }

            $rendered = View::make($view, SectionRegistry::resolve($section))->render();

            $anchor = $anchors[$section->type] ?? null;
            if ($anchor && ! in_array($anchor, $usedAnchors, true)) {
                $usedAnchors[] = $anchor;
                $rendered = '<div id="'.e($anchor).'" class="scroll-mt-24">'.$rendered.'</div>';
            }

            $html .= $rendered;
        }

        return new HtmlString($html);
    }
}
