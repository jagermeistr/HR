<?php

namespace App\Livewire\Admin\Feedback;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public Feedback $feedback;
    public $response = '';

    protected $rules = [
        'response' => 'required|string|min:5|max:500'
    ];

    public function mount(Feedback $feedback)
    {
        $user = Auth::user();
        
        if (!$feedback->canView($user)) {
            abort(403, 'Unauthorized action.');
        }

        $this->feedback = $feedback->load('responses.user');
    }

    public function addResponse()
    {
        $this->validate();

        if (!$this->feedback->canRespond(Auth::user())) {
            abort(403, 'Unauthorized action.');
        }

        $this->feedback->responses()->create([
            'user_id' => Auth::id(),
            'response' => $this->response
        ]);

        $this->response = '';
        $this->feedback->refresh();

        session()->flash('message', 'Response added successfully!');
    }

    public function render()
    {
        return view('livewire.admin.feedback.show');
    }
}