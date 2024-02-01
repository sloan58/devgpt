<div x-data="chatApp()">
    <div class="flex flex-col h-screen justify-between bg-gray-700 w-screen overflow-y-auto py-8">
        @livewire('navigation-menu')
        <div id="messages" class="flex-col space-y-4 w-1/2 mx-auto">
            <div class="flex-col space-y-2 py-4 max-h-24 mx-2">
                <template x-for="message in messages" hidden>
                    <div class="flex-col justify-start py-2 text-gray-200" :key="Date.now()">
                        <div class="flex py-2" :class="{
                            'justify-start': message.role === 'user',
                            'justify-start': message.role === 'assistant'
                        }">
                            <div
                                x-text="message.role === 'user' ? 'U' : 'AI'"
                                class="flex items-center justify-center h-12 w-12 font-bold rounded-full"
                                :class="{
                                    'text-gray-700 bg-gray-200': message.role === 'user',
                                    'text-gray-200 bg-gray-800': message.role === 'assistant'
                                }"
                            ></div>
                        </div>
                        <div style="white-space: pre-line" x-html="formattedContent(message.content)"></div>
                    </div>
                </template>
                <div x-cloak x-show="receiving" class="flex-col justify-start py-2 text-gray-200">
                    <div class="flex py-2">
                        <div class="flex items-center justify-center h-12 w-12 font-bold rounded-full text-gray-200 bg-gray-800">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"></span>
                            </span>
                        </div>
                    </div>
                    <div id="answer" wire:stream="answer" style="white-space: pre-line">{!! $answer !!}</div>
                </div>
            </div>
        </div>
        <footer class="text-center pb-2 fixed inset-x-0 bottom-0 bg-gray-700">
            <div class="flex justify-center">
                <div class="flex-col w-1/2 space-y-4">
                    <x-input @keyup.enter.prevent="send" x-model="prompt" placeholder="Please enter your prompt..." class="w-full bg-gray-700 text-slate-200 border-slate-500" />
                    <div class="flex justify-end space-x-2">
                        <x-button @click="clear" type="text">Clear</x-button>
                        <x-button @click="send" type="text">Submit</x-button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script>
        const scrollToBottom = () => {
            let messages = document.getElementById("messages");
            messages.scrollTop = messages.scrollHeight;
        };

        // Select the node that will be observed for mutations
        // const targetNode = document.getElementById("answer");
        //
        // // Options for the observer (which mutations to observe)
        // const config = { attributes: true, childList: true, subtree: true };
        //
        // // Callback function to execute when mutations are observed
        // const callback = (mutationList, observer) => {
        //     for (const mutation of mutationList) {
        //         scrollToBottom();
        //         hljs.highlightAll();
        //     }
        // };
        //
        // // Create an observer instance linked to the callback function
        // const observer = new MutationObserver(callback);
        //
        // // Start observing the target node for configured mutations
        // observer.observe(targetNode, config);

        // // Later, you can stop observing
        // observer.disconnect();


        let chatApp = () => {
            return {
                receiving: @entangle('receiving'),
                messages: @entangle('messages'),
                answer: @entangle('answer'),
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
