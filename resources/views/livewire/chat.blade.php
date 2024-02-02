<div>
    <div x-data="chatApp()" class="flex-1 justify-between flex flex-col h-screen">
        @livewire('navigation-menu')
        <template x-if="!messages.length" hidden>
            <div class="flex flex-1 grow justify-center">
                <div class="flex grow items-center justify-center h-16 mt-64">
                    <x-application-mark class="block h-9 w-auto" />
                    <p class="text-4xl ml-2 font-semibold text-gray-400">DevGPT</p>
                </div>
            </div>
        </template>
        <div id="messages" class="flex flex-1 flex-col space-y-4 p-3 overflow-y-auto scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-gray-lighter scrollbar-w-2 scrolling-touch">
            <div class="w-3/4 lg:w-1/2 mx-auto pt-4">
                <template x-for="(message, index) in messages" hidden>
                    <div class="chat-message group" :key="Date.now()">
                        <div class="flex-col justify-start my-4 text-gray-200">
                            <div class="flex py-2 justify-start">
                                <div
                                    x-text="message.role === 'user' ? 'Me' : 'AI'"
                                    class="flex items-center justify-center h-12 w-12 font-bold rounded-full"
                                    :class="{
                                    'text-gray-700 bg-gray-200': message.role === 'user',
                                    'text-gray-200 bg-gray-800': message.role === 'assistant'
                                }"
                                ></div>
                            </div>
                            <div :class="{
                                'mb-8': message.role === 'user'
                            }" style="white-space: pre-line" x-html="formattedContent(message.content)"></div>
                            <template x-if="message.role === 'assistant'" hidden>
                                <div class="flex group space-x-2" :class="{
                                    'transition ease-in-out delay-100 opacity-0 group-hover:opacity-100': index + 1 !== messages.length
                                    }">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 hover:text-gray-200 mt-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 hover:text-gray-200 mt-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                                    </svg>
                                    <template x-if="index + 1 === messages.length" hidden>
                                        <svg @click="rerun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 hover:text-gray-200 mt-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
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
        <template x-if="!messages.length" hidden>
            <div class="flex justify-center w-3/4 lg:w-1/2 mx-auto">
                <div class="flex flex-1 group justify-center space-x-2">
                    <div @click="send('Show me how to create a MongoDB aggregation query')" class="grow group-hover:cursor-pointer bg-gray-700 border rounded-lg border-gray-500 hover:border-gray-400 p-4">
                        <p class="text-gray-200">MongDB Queries</p>
                        <p class="text-gray-400 text-xs font-semibold">Generate a MongoDB aggregation</p>
                    </div>
                    <div @click="send('Create a regular expression to capture an IPv4 address and explain the functions.')" class="grow group-hover:cursor-pointer bg-gray-700 border rounded-lg border-gray-500 hover:border-gray-400 p-4">
                        <p class="text-gray-200">Regular Expressions</p>
                        <p class="text-gray-400 text-xs font-semibold">Create a regex to capture an IPv4 addresses</p>
                    </div>
                    <div @click="send('Show me how to create a Laravel Livewire component')" class="grow group-hover:cursor-pointer bg-gray-700 border rounded-lg border-gray-500 hover:border-gray-400 p-4">
                        <p class="text-gray-200">Generate Code</p>
                        <p class="text-gray-400 text-xs font-semibold">Create a Laravel Livewire component</p>
                    </div>
                </div>
            </div>
        </template>
        <div class="py-4 mb-2 sm:mb-0">
            <div class="relative flex justify-center">
                <label for="default-search" class=" mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative w-full mx-4 md:mx-0 lg:w-1/2">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <input @keyup.enter.prevent="send(null)" x-model="prompt" type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 rounded-lg bg-gray-700 border-gray-600 placeholder-gray-400 text-white border-gray-600 focus:border-gray-600 focus:ring-0" placeholder="Ask me something promptly..." required>
                    <x-button @click="send(null)" type="text" class="hidden md:block absolute end-2.5 bottom-2.5 !bg-gray-800 !text-gray-400 hover:!text-gray-200">Send it</x-button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const scrollToBottom = () => {
            let messages = document.getElementById("messages");
            messages.scrollTop = messages.scrollHeight;
        };

        const targetNode = document.getElementById("answer");

        let observer = new MutationObserver((mutationList, observer) => {
            for (const mutation of mutationList) {
                scrollToBottom();
                // hljs.highlightAll(); // Causes a bunch of console errors for re-highlighting.
            }
        });

        observer.observe(targetNode, {
            characterData: true,
            subtree: true,
            attributes: true,
            childList: true
        });

        let chatApp = () => {
            return {
                receiving: @entangle('receiving'),
                messages: @entangle('messages'),
                answer: @entangle('answer'),
                prompt: '',
                formattedContent: function(content) {
                    return content.replace(/```\w+\s([\s\S]+?)```/g, (match, p1) => `<pre class="my-2 bg-slate-200 rounded px-4">${p1}</pre>`)
                },
                send: async function(message = null) {
                    let content = message ?? this.prompt;
                    if (content === '') {
                        return;
                    }
                    this.receiving = true;
                    await this.messages.push({
                        role: 'user',
                        content: message ?? this.prompt,
                    });
                    scrollToBottom();
                    this.prompt = '';
                    @this.call('send');
                    scrollToBottom();
                },
                clear: function () {
                    this.messages = [];
                },
                rerun: async function () {
                    this.receiving = true;
                    if (typeof this.messages === 'object') {
                        this.messages = Object.values(this.messages);
                    }
                    await this.messages.pop();
                    scrollToBottom();
                    await @this.call('send');
                    if (typeof this.messages === 'object') {
                        this.messages = Object.values(this.messages);
                    }
                    scrollToBottom();
                }
            }
        }
    </script>
{{--    <style>--}}
{{--        .scrollbar-w-2::-webkit-scrollbar {--}}
{{--            width: 0.50rem;--}}
{{--            height: 0.50rem;--}}
{{--        }--}}

{{--        .scrollbar-track-gray-lighter::-webkit-scrollbar-track {--}}
{{--            --bg-opacity: 1;--}}
{{--            background-color: #f7fafc;--}}
{{--            background-color: rgba(247, 250, 252, var(--bg-opacity));--}}
{{--        }--}}

{{--        .scrollbar-thumb-blue::-webkit-scrollbar-thumb {--}}
{{--            --bg-opacity: 1;--}}
{{--            background-color: #edf2f7;--}}
{{--            background-color: rgba(237, 242, 247, var(--bg-opacity));--}}
{{--        }--}}

{{--        .scrollbar-thumb-rounded::-webkit-scrollbar-thumb {--}}
{{--            border-radius: 0.25rem;--}}
{{--        }--}}
{{--    </style>--}}

{{--    <script>--}}
{{--        const el = document.getElementById('messages')--}}
{{--        el.scrollTop = el.scrollHeight--}}
{{--    </script>--}}
</div>
