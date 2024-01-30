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

    private string $startCodeBlock = '/```\w+\s?(.*)\n/';

    private string $endCodeBlock = '/```\n\n/';

    private bool $buildingCodeBlock = false;

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

            // If we're building a code block, remove the trailing closing tags
            // so that we can add more code content.
            if ($this->buildingCodeBlock) {
                $this->answer = preg_replace('/<\/xmp><\/code><\/pre>$/', '', $this->answer);
            }

            // Concatenate the stream content to our answer.
            $this->answer .= $response->choices[0]->delta->content;

            // If this regex matches, we're starting a code block.
            if (preg_match($this->startCodeBlock, $this->answer)) {
                // Strip out markdown syntax (```) and replace with code/pre tags for UI format.
                $this->answer = preg_replace(
                    $this->startCodeBlock,
                    "<pre class=\"my-2 bg-slate-200 rounded px-4 pb-2\"><code><xmp>$1</xmp></code></pre>",
                    $this->answer
                );

                // We're now building a code block.
                $this->buildingCodeBlock = true;
            }

            // If this regex matches, we're ending a code block.
            if (preg_match($this->endCodeBlock, $this->answer)) {
                // Replace the trailing newlines with proper closing tags.
                $this->answer = preg_replace('/```\n?\n?$/', '</xmp></code></pre>', $this->answer);

                // We're no longer building a code block.
                $this->buildingCodeBlock = false;
            }

            // If we're building a code block but not yet finished, we need to replace the closing tags.
            if ($this->buildingCodeBlock and ! preg_match('/<\/xmp><\/code><\/pre>$/', $this->answer)) {
                $this->answer .= '</xmp></code></pre>';
            }

            // Send the stream to the UI.
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
