<?php

namespace App\Jobs;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class PublishScheduledPosts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array
     */
    public function middleware()
    {
        return [new WithoutOverlapping('publish_scheduled_posts')];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Find all scheduled posts (status = 1) where scheduled_at is in the past
        $posts = Post::where('status', 1)
            ->where('scheduled_at', '<=', Carbon::now()->addHour())
            ->get();

        $count = $posts->count();

        if ($count > 0) {
            Log::info("Publishing {$count} scheduled posts that have reached their scheduled time.");

            foreach ($posts as $post) {
                // Update status to published (status = 2)
                // محاكاة عملية النشر لكل منصة
                foreach ($post->platforms as $platform) {
                    // بدل هذا بـ logic حقيقي لو في المستقبل
                    $post->platforms()->updateExistingPivot($platform->id, [
                        'platform_status' => 1
                    ]);
                }
                $post->status = 2;
                $post->save();

                Log::info("Published post ID: {$post->id}, scheduled for: {$post->scheduled_at}");

                // Here you could add additional logic for actual publishing to social platforms
                // For example, dispatch platform-specific jobs
                // $this->dispatchPlatformPublishingJobs($post);
            }
        } else {
            Log::info("No scheduled posts found that need publishing.");
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Failed to publish scheduled posts: " . $exception->getMessage());
    }
}

