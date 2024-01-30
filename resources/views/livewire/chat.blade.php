<div x-data="chatApp()" class="flex-col space-y-4 p-8">
    <div class="flex justify-center border-b pb-4">
        <div class="text-indigo-500/90 font-semibold text-2xl">ChatGPT</div>
    </div>
    <div id="messages" class="h-[60vh] overflow-y-scroll">
        <div class="flex-col divide-y space-y-2 py-4 max-h-24 mx-2">
            <template x-for="message in messages" hidden>
                <div class="flex-col justify-start py-2" :class="{
                        'bg-slate-100 px-2': message.role === 'assistant'
                    }" :key="Date.now()">
                    <div class="flex py-2" :class="{
                        'justify-start': message.role === 'user',
                        'justify-end': message.role === 'assistant'
                    }">
                        <div
                            x-text="message.role === 'user' ? 'You' : 'OpenAI'"
                            class="font-semibold bg-gray-200 px-2 py-1 rounded"
                            :class="{
                                'text-indigo-500 bg-gray-200': message.role === 'user',
                                'text-indigo-300 bg-slate-700': message.role === 'assistant'
                            }"
                        ></div>
                    </div>
                    <div style="white-space: pre-line" x-html="formattedContent(message.content)"></div>
                </div>
            </template>
            <div x-cloak x-show="receiving" class="flex-col justify-start py-2">
                <div class="flex py-2 justify-end">
                    <div class="bg-slate-700 text-slate-200 font-semibold px-2 py-1 rounded">OpenAI</div>
                </div>
                <div id="answer" class="text-slate-600" wire:stream="answer" style="white-space: pre-line">{!! $answer !!}</div>
            </div>
        </div>
    </div>
    <div class="flex-col space-y-2">
        <x-input @keyup.enter.prevent="send" x-model="prompt" placeholder="Please enter your prompt..." class="w-full" />
        <div class="flex justify-end space-x-2">
            <x-button @click="clear" type="text">Clear</x-button>
            <x-button @click="send" type="text">Submit</x-button>
        </div>
    </div>
    <script>
        const scrollToBottom = () => {
            let messages = document.getElementById("messages");
            messages.scrollTop = messages.scrollHeight;
        };

        // Select the node that will be observed for mutations
        const targetNode = document.getElementById("answer");

        // Options for the observer (which mutations to observe)
        const config = { attributes: true, childList: true, subtree: true };

        // Callback function to execute when mutations are observed
        const callback = (mutationList, observer) => {
            for (const mutation of mutationList) {
                scrollToBottom();
            }
        };

        // Create an observer instance linked to the callback function
        const observer = new MutationObserver(callback);

        // Start observing the target node for configured mutations
        observer.observe(targetNode, config);

        // // Later, you can stop observing
        // observer.disconnect();


        let chatApp = () => {
            return {
                receiving: @entangle('receiving'),
                messages: @entangle('messages'),
                prompt: '',
                formattedContent: function(content) {
                    return content.replace(/```\w+\s([\s\S]+?)```/g, (match, p1) => `<pre class="my-2 bg-slate-200 rounded px-4">${p1}</pre>`)
                },
                send: async function() {
                    this.receiving = true;
                    await this.messages.push({
                        role: 'user',
                        content: this.prompt,
                    });
                    scrollToBottom();
                    this.prompt = '';
                    @this.call('send');
                    scrollToBottom();
                },
                clear: function () {
                    this.messages = [];
                },
            }
        }
    </script>
</div>
