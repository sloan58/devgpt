<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Contracts\View\View;

class Chat extends Component
{
    public array $messages = [];
    public bool $receiving = false;

    public string $answer = '';

    public function send()
    {
        $this->receiving = true;

        $stream = OpenAI::chat()->createStreamed([
            'model' => 'gpt-3.5-turbo',
            'messages' => $this->messages,
        ]);

        foreach ($stream as $response) {
            // We're finished streaming - break.
            if ($response->choices[0]->finishReason) {
                break;
            }

            // Append the stream to the answer.
            $this->answer .= $response->choices[0]->delta->content;

            // Send the stream to the UI.
            $this->stream(to: 'answer', content: $this->answer, replace: true);
        }

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $this->answer,
        ];

        $this->answer = '';
        $this->receiving = false;
    }

    public function render(): View
    {
//        $this->messages = config('messages');
        return view('livewire.chat')->layout('layouts.chat');
    }
}
