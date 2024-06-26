<?php

namespace LinaPhp\Lina\Commands;

use LinaPhp\Lina\Content;
use LinaPhp\Lina\ContentFinder;
use LinaPhp\Lina\MarkdownRenderer;
use LaravelZero\Framework\Commands\Command;

class BuildCommand extends Command
{
    protected $signature = 'build';

    protected $description = 'build your app to html files';

    public function handle(): int
    {
        $finder = app(ContentFinder::class);

        $items = $finder->index();
        $renderer = app(MarkdownRenderer::class);

        $this->warn('Building your site...');
        $count = 0;
        $startAt = microtime(true);

        foreach ($items as $item) {
            $this->buildItem($item, $renderer);
            $count++;
        }

        $this->info(
            sprintf("Built %s pages in %s! 🚀", $count, round(microtime(true) - $startAt, 4) * 1000 . 'ms')
        );

        return 0;
    }

    protected function buildItem(Content $item, MarkdownRenderer $renderer): void
    {
        $directory = getcwd() . '/public/' . ($item->url() === '/' ? 'index.html' : ($item->url() . '.html'));

        if (!is_dir(dirname($directory))) {
            mkdir(dirname($directory), 0755, true);
        }

        file_put_contents($directory, $renderer->render(content_path($item->path)));

        if ($this->getOutput()->isVerbose()) {
            $this->line('Built html for: ' . $item->path);
        }
    }
}
