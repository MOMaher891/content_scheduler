<?php

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPosts;
use Illuminate\Console\Command;

class RunPostPublishing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish';
    protected $description = 'Run scheduled post publishing job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running scheduled post publishing job...');
        dispatch(new PublishScheduledPosts());
    }
}
