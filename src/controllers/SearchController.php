<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Models\News;
use Dorguzen\Models\Portfolio;

class SearchController extends DGZ_Controller
{
    public function __construct(
        private News      $news,
        private Portfolio $portfolio
    ) {
        parent::__construct();
    }

    public function getDefaultAction(): string
    {
        return 'search';
    }

    public function search(): void
    {
        $val     = new DGZ_Validate();
        $keyword = $val->fix_string(trim($_GET['search_keyword'] ?? ''));

        $results = ['news' => [], 'portfolio' => [], 'blog' => []];

        if ($keyword !== '') {
            $results['news']      = $this->news->search($keyword);
            $results['portfolio'] = $this->portfolio->search($keyword);

            if (config('app.modules.blog') === 'on') {
                $blogPost        = container(\Dorguzen\Modules\Blog\Models\BlogPost::class);
                $results['blog'] = $blogPost->searchPublished($keyword);
            }
        }

        $view = DGZ_View::getView('search', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show([
            'keyword' => $keyword,
            'results' => $results,
        ]);
    }
}
