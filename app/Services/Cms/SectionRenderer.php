<?php

namespace App\Services\Cms;

use App\Models\Page;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\View;

/**
 * Renders a Page's active sections, in order, each through its Blade partial
 * at resources/views/sections/{view}.blade.php.
 */
class SectionRenderer
{
    public function render(Page $page): HtmlString
    {
        $html = '';

        foreach ($page->activeSections()->get() as $section) {
            $def = SectionRegistry::get($section->type);
            $view = 'sections.'.($def['view'] ?? $section->type);

            if (! View::exists($view)) {
                continue;
            }

            $html .= View::make($view, SectionRegistry::resolve($section))->render();
        }

        return new HtmlString($html);
    }
}
