<?php

namespace App\Livewire;

use App\Models\Platform;
use App\Models\UserPlatform;
use Livewire\Component;
use Livewire\Attributes\Title;

class UserPlatforms extends Component
{
    #[Title('My Platforms')]

    public $platform_id;
    public $status = 0;
    public $message;
    public $messageType;

    public function mount()
    {
        // Initialize with default values
    }

    public function render()
    {
        $platforms = Platform::with('platformType')->get();
        $user_platforms = UserPlatform::where('user_id', auth()->id())
            ->with('platform.platformType')
            ->get();

        return view('livewire.user-platforms', compact('platforms', 'user_platforms'));
    }

    public function addPlatform()
    {
        $this->validate([
            'platform_id' => 'required|exists:platforms,id',
        ]);

        // Check if user already has this platform
        $existingPlatform = UserPlatform::where('user_id', auth()->id())
            ->where('platform_id', $this->platform_id)
            ->first();

        if ($existingPlatform) {
            $this->message = 'You already have this platform added to your account.';
            $this->messageType = 'error';
            return;
        }

        // Add platform to user
        UserPlatform::create([
            'user_id' => auth()->id(),
            'platform_id' => $this->platform_id,
            'status' => $this->status,
        ]);

        // Reset form
        $this->reset(['platform_id', 'status']);

        $this->message = 'Platform added successfully!';
        $this->messageType = 'success';
    }

    public function toggleStatus($userPlatformId)
    {
        $userPlatform = UserPlatform::where('id', $userPlatformId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$userPlatform) {
            $this->message = 'Platform not found.';
            $this->messageType = 'error';
            return;
        }

        // Toggle status (0 to 1, 1 to 0)
        $userPlatform->status = $userPlatform->status ? 0 : 1;
        $userPlatform->save();

        $this->message = 'Platform status updated!';
        $this->messageType = 'success';
    }

    public function removePlatform($userPlatformId)
    {
        $userPlatform = UserPlatform::where('id', $userPlatformId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$userPlatform) {
            $this->message = 'Platform not found.';
            $this->messageType = 'error';
            return;
        }

        $userPlatform->delete();

        $this->message = 'Platform removed successfully!';
        $this->messageType = 'success';
    }
}

