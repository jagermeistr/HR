<?php

namespace App\Livewire\Admin\Feedback;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $activeTab = 'sent'; // Changed default to 'sent' since you're sending feedback

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function deleteFeedback($feedbackId)
    {
        $feedback = Feedback::findOrFail($feedbackId);
        $user = Auth::user();
        
        if (!$feedback->canDelete($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $feedback->delete();
        
        session()->flash('success', 'Feedback deleted successfully!');
    }

    public function render()
    {
        $user = Auth::user();
        
        // Since we're now using employee_id instead of receiver_id, 
        // we only need to show sent feedback (feedback you've sent to employees)
        $sentFeedback = Feedback::with(['employee.designation.department', 'responses'])
            ->fromSender($user->id)
            ->sent()
            ->latest()
            ->paginate(10);

        // For now, received feedback will be empty since employees don't have user accounts
        $receivedFeedback = collect();

        return view('livewire.admin.feedback.index', [
            'receivedFeedback' => $receivedFeedback,
            'sentFeedback' => $sentFeedback,
        ]);
    }
}