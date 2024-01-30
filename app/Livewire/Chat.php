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
            if ($response->choices[0]->finishReason) {
                break;
            }
            $this->answer .= $response->choices[0]->delta->content;
            
            $this->answer = preg_replace(
                '/```\w+\s([\s\S]+?)```/',
                "<pre class=\"my-2 bg-slate-200 rounded p-4\">$1</pre>",
                $this->answer
            );
            $this->stream(to: 'answer', content: $this->answer, replace: true);
        }

        if ($this->answer) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => $this->answer,
            ];
        }

        $this->answer = '';
        $this->receiving = false;
    }

    public function render(): View
    {
        return view('livewire.chat');
    }
}
