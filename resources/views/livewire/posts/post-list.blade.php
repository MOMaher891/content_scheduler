    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages -->
            @if($message)
                <div class="mb-4 px-4 py-2 border rounded-md {{ $messageType === 'success' ? 'border-green-500 bg-green-100 text-green-700' : 'border-red-500 bg-red-100 text-red-700' }}">
                    {{ $message }}
                </div>
            @endif

            <!-- Create/Edit Post Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $editMode ? 'Edit Post' : 'Create New Post' }}
                    </h3>

                    <form wire:submit.prevent="{{ $editMode ? 'update' : 'create' }}" class="space-y-4">
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea id="content" wire:model="content" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                            @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Image (Optional)</label>
                            <input type="file" id="image" wire:model="image" class="mt-1 block w-full">
                            @error('image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                            @if ($image)
                                <div class="mt-2">
                                    <img src="{{ $image->temporaryUrl() }}" class="h-24 w-auto object-cover rounded">
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule Date</label>
                            <input type="datetime-local" id="scheduled_at" wire:model="scheduled_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('scheduled_at') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="0">Draft</option>
                                <option value="1">Scheduled</option>
                                <option value="2">Published</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Platforms</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                @foreach($platforms as $platform)
                                    <label class="inline-flex items-center bg-gray-50 p-2 rounded border">
                                        <input type="checkbox" wire:model="selectedPlatforms" value="{{ $platform->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $platform->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedPlatforms') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit" style="background-color:#4CAF50;margin-top:15px" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ $editMode ? 'Update Post' : 'Create Post' }}
                            </button>

                            @if($editMode)
                                <button type="button" wire:click="cancelEdit" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200" style="min-height: 420px;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Your Posts</h3>

                        <!-- Filters -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center px-4 py-2 bg-gray-100 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filters
                                @if($filterStatus !== '' || $filterDateFrom || $filterDateTo)
                                    <span class="ml-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                        {{ ($filterStatus !== '' ? 1 : 0) + ($filterDateFrom || $filterDateTo ? 1 : 0) }}
                                    </span>
                                @endif
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-10 p-4 border">
                                <div class="space-y-4">
                                    <div>
                                        <label for="filterStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                        <select id="filterStatus" wire:model.live="filterStatus" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="">All Statuses</option>
                                            <option value="0">Draft</option>
                                            <option value="1">Scheduled</option>
                                            <option value="2">Published</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="filterDateFrom" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                        <input type="date" id="filterDateFrom" wire:model.live="filterDateFrom" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>

                                    <div>
                                        <label for="filterDateTo" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                        <input type="date" id="filterDateTo" wire:model.live="filterDateTo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>

                                    <div class="pt-2 flex justify-end">
                                        <button wire:click="resetFilters" class="text-sm text-gray-600 hover:text-gray-900">
                                            Reset Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($posts->isEmpty())
                        <p class="text-gray-500">No posts found matching your filters.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($posts as $post)
                                <div class="border rounded-lg overflow-hidden">
                                    <div class="p-4 bg-gray-50 flex justify-between items-center">
                                        <div>
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $this->getStatusClass($post->status) }}">
                                                {{ $statusLabels[$post->status] }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-500">
                                                Scheduled: {{ \Carbon\Carbon::parse($post->scheduled_at)->format('M d, Y h:i A') }}
                                            </span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button wire:click="edit({{ $post->id }})" class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </button>
                                            <button wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this post?" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <p class="text-gray-800">{{ $post->content }}</p>

                                        @if($post->image_path)
                                            <div class="mt-3">
                                                <img src="{{ Storage::url($post->image_path) }}" class="h-40 w-auto object-cover rounded">
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <h4 class="text-sm font-medium text-gray-700 mb-1">Platforms:</h4>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($post->platforms as $platform)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        {{ $platform->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="mt-4">
                                {{ $posts->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            @if($confirmingDelete)
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-md w-full">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h3>
                            <p class="text-gray-600 mb-4">Are you sure you want to delete this post? This action cannot be undone.</p>

                            <div class="flex justify-end space-x-2">
                                <button wire:click="cancelDelete" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </button>
                                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>


