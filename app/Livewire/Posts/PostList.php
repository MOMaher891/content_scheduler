<?php

namespace App\Livewire\Posts;

use App\Models\Post;
use App\Models\Platform;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

class PostList extends Component
{
    use WithPagination;
    use WithFileUploads;

    #[Title('My Posts')]

    public $content;
    public $image;
    public $scheduled_at;
    public $status = 0;
    public $selectedPlatforms = [];
    public $editMode = false;
    public $postId;
    public $message;
    public $messageType;
    public $confirmingDelete = false;
    public $deleteId;

    // Filter properties
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    // Status constants for better readability
    const STATUS_DRAFT = 0;
    const STATUS_SCHEDULED = 1;
    const STATUS_PUBLISHED = 2;

    protected $rules = [
        'content' => 'required|string|min:3',
        'image' => 'nullable|image|max:1024',
        'scheduled_at' => 'required|date|after_or_equal:now',
        'status' => 'required|integer|in:0,1,2',
        'selectedPlatforms' => 'required|array|min:1',
    ];

    // Reset pagination when filters change
    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom()
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filterStatus', 'filterDateFrom', 'filterDateTo']);
        $this->resetPage();
    }

    public function render()
    {
        $postsQuery = Post::where('user_id', auth()->id());

        // Apply status filter if selected
        if ($this->filterStatus !== '') {
            $postsQuery->where('status', $this->filterStatus);
        }

        // Apply date range filter if provided
        if ($this->filterDateFrom) {
            $postsQuery->whereDate('scheduled_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $postsQuery->whereDate('scheduled_at', '<=', $this->filterDateTo);
        }

        $posts = $postsQuery->with(['platforms'])
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $platforms = Platform::whereHas('userPlatforms', function ($query) {
            $query->where('user_id', auth()->id())
                ->where('status', 1); // Only active platforms
        })->get();

        return view('livewire.posts.post-list', [
            'posts' => $posts,
            'platforms' => $platforms,
            'statusLabels' => [
                self::STATUS_DRAFT => 'Draft',
                self::STATUS_SCHEDULED => 'Scheduled',
                self::STATUS_PUBLISHED => 'Published'
            ]
        ]);
    }

    public function create()
    {
        $this->validate();

        // Handle image upload
        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('post-images', 'public');
        }

        // Create post
        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $this->content,
            'image_path' => $imagePath,
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
        ]);

        // Attach platforms
        $post->platforms()->attach($this->selectedPlatforms, ['platform_status' => 0]);

        $this->reset(['content', 'image', 'scheduled_at', 'status', 'selectedPlatforms']);
        $this->message = 'Post created successfully!';
        $this->messageType = 'success';
    }

    public function edit($id)
    {
        $post = Post::where('user_id', auth()->id())->findOrFail($id);

        $this->postId = $post->id;
        $this->content = $post->content;
        $this->scheduled_at = $post->scheduled_at;
        $this->status = $post->status;
        $this->selectedPlatforms = $post->platforms->pluck('id')->toArray();

        $this->editMode = true;
    }

    public function update()
    {
        $this->validate([
            'content' => 'required|string|min:3',
            'image' => 'nullable|image|max:1024',
            'scheduled_at' => 'required|date',
            'status' => 'required|integer|in:0,1,2',
            'selectedPlatforms' => 'required|array|min:1',
        ]);

        $post = Post::where('user_id', auth()->id())->findOrFail($this->postId);

        // Handle image upload
        $imagePath = $post->image_path;
        if ($this->image) {
            // Delete old image if exists
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            $imagePath = $this->image->store('post-images', 'public');
        }

        // Update post
        $post->update([
            'content' => $this->content,
            'image_path' => $imagePath,
            'scheduled_at' => $this->scheduled_at,
            'status' => $this->status,
        ]);

        // Sync platforms
        $post->platforms()->sync($this->selectedPlatforms);

        $this->reset(['content', 'image', 'scheduled_at', 'status', 'selectedPlatforms', 'editMode', 'postId']);
        $this->message = 'Post updated successfully!';
        $this->messageType = 'success';
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->deleteId = $id;
    }

    public function delete($id)
    {
        $post = Post::where('user_id', auth()->id())->findOrFail($id);

        // Delete image if exists
        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }

        $post->delete();

        $this->confirmingDelete = false;
        $this->deleteId = null;
        $this->message = 'Post deleted successfully!';
        $this->messageType = 'success';
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->deleteId = null;
    }

    public function cancelEdit()
    {
        $this->reset(['content', 'image', 'scheduled_at', 'status', 'selectedPlatforms', 'editMode', 'postId']);
    }

    public function getStatusClass($status)
    {
        return [
            self::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
            self::STATUS_SCHEDULED => 'bg-blue-100 text-blue-800',
            self::STATUS_PUBLISHED => 'bg-green-100 text-green-800',
        ][$status] ?? 'bg-gray-100 text-gray-800';
    }
}



