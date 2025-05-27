<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Post Statistics Cards -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Post Statistics</h3>
                    @php
                        $data = Carbon\Carbon::now()->addHour();
                    @endphp
                    <h1 wire:poll>{{ $data }}</h1>
                    @php
                        $draftPosts = App\Models\Post::where('user_id', auth()->id())->where('status', 0)->count();
                        $scheduledPosts = App\Models\Post::where('user_id', auth()->id())->where('status', 1)->count();
                        $publishedPosts = App\Models\Post::where('user_id', auth()->id())->where('status', 2)->count();
                        $totalPosts = $draftPosts + $scheduledPosts + $publishedPosts;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg text-center shadow">
                            <div class="text-3xl font-bold text-gray-800">{{ $draftPosts }}</div>
                            <div class="text-sm text-gray-600">Draft Posts</div>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ $totalPosts > 0 ? round(($draftPosts / $totalPosts) * 100) : 0 }}% of total
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('posts.index') }}?status=0" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    View all drafts
                                </a>
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg text-center shadow">
                            <div class="text-3xl font-bold text-blue-800">{{ $scheduledPosts }}</div>
                            <div class="text-sm text-blue-600">Scheduled Posts</div>
                            <div class="mt-2 text-xs text-blue-500">
                                {{ $totalPosts > 0 ? round(($scheduledPosts / $totalPosts) * 100) : 0 }}% of total
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('posts.index') }}?status=1" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    View all scheduled
                                </a>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg text-center shadow">
                            <div class="text-3xl font-bold text-green-800">{{ $publishedPosts }}</div>
                            <div class="text-sm text-green-600">Published Posts</div>
                            <div class="mt-2 text-xs text-green-500">
                                {{ $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100) : 0 }}% of total
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('posts.index') }}?status=2" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    View all published
                                </a>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg text-center shadow">
                            <div class="text-3xl font-bold text-purple-800">{{ $totalPosts }}</div>
                            <div class="text-sm text-purple-600">Total Posts</div>
                            <div class="mt-2 text-xs text-purple-500">
                                All posts combined
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('posts.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    View all posts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scheduled Posts Graph -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Scheduled Posts Timeline</h3>

                    @php
                        // Get scheduled posts for the next 30 days
                        $startDate = now();
                        $endDate = now()->addDays(30);

                        $scheduledPostsByDate = App\Models\Post::where('user_id', auth()->id())
                            ->where('status', 1)
                            ->whereBetween('scheduled_at', [$startDate, $endDate])
                            ->get()
                            ->groupBy(function($post) {
                                return \Carbon\Carbon::parse($post->scheduled_at)->format('Y-m-d');
                            });

                        // Create an array of dates for the next 30 days
                        $dateRange = [];
                        $currentDate = $startDate->copy();
                        while ($currentDate <= $endDate) {
                            $dateKey = $currentDate->format('Y-m-d');
                            $dateRange[$dateKey] = [
                                'date' => $currentDate->format('M d'),
                                'count' => isset($scheduledPostsByDate[$dateKey]) ? $scheduledPostsByDate[$dateKey]->count() : 0
                            ];
                            $currentDate->addDay();
                        }

                        // Find the maximum count for scaling
                        $maxCount = 1; // Minimum 1 to avoid division by zero
                        foreach ($dateRange as $data) {
                            if ($data['count'] > $maxCount) {
                                $maxCount = $data['count'];
                            }
                        }
                    @endphp

                    @if(count($scheduledPostsByDate) > 0)
                        <div class="mt-4">
                            <div class="overflow-x-auto">
                                <div class="min-w-max">
                                    <div class="flex items-end space-x-2 h-64">
                                        @foreach($dateRange as $date => $data)
                                            <div class="flex flex-col items-center">
                                                <div class="text-xs text-gray-500 mb-1">{{ $data['count'] }}</div>
                                                <div class="w-8 bg-blue-{{ $data['count'] > 0 ? '500' : '100' }} rounded-t"
                                                     style="height: {{ $data['count'] > 0 ? (($data['count'] / $maxCount) * 150) + 20 : 5 }}px;">
                                                </div>
                                                <div class="text-xs mt-1 {{ $date == now()->format('Y-m-d') ? 'font-bold text-blue-600' : 'text-gray-500' }}">
                                                    {{ $data['date'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4 text-sm text-gray-500">
                                Scheduled posts for the next 30 days
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-500">No scheduled posts for the next 30 days.</p>
                            <a href="{{ route('posts.index') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-800">
                                Create a scheduled post
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>






