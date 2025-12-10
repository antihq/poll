<x-layouts::site title="Polls in emails. Not platform lock-in" :dark="false">
    <flux:spacer class="my-10 sm:my-18 md:my-26 lg:my-24" />

    <div>
        <h1
            class="text-6xl/[0.9] font-medium tracking-tight text-balance text-zinc-950 sm:text-8xl/[0.8] md:text-9xl/[0.8]"
        >
            Polls in emails.
            <br />
            Not platform lock-in.
        </h1>

        <p class="mt-8 text-xl/7 font-medium text-zinc-950/75 sm:text-2xl/8">
            Create
            <strong class="text-zinc-950">1000 polls FREE</strong>
            with AntiPoll. After that it's
            <a href="#pricing" class="font-semibold text-zinc-950 underline">only $20/month.</a>
        </p>

        <div
            style="--width: 1257; --height: 705"
            class="relative mt-16 aspect-[var(--width)/var(--height)] h-144 [--radius:var(--radius-xl)] sm:mx-auto sm:h-auto sm:w-304"
        >
            <div
                class="absolute -inset-(--padding) rounded-[calc(var(--radius)+var(--padding))] shadow-xs ring-1 ring-black/5 [--padding:--spacing(2)]"
            ></div>
            <img
                alt="AntiPoll - Polls in emails. Not platform lock-in."
                src="{{ Vite::asset('resources/screenshots/app.webp') }}"
                class="h-full rounded-(--radius) shadow-2xl ring-1 ring-black/10"
            />
        </div>

        <flux:spacer class="my-32" />

        <div
            class="overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 max-lg:rounded-4xl lg:col-span-3 lg:rounded-4xl"
        >
            <div class="grid grid-cols-1 text-lg font-medium text-zinc-600 lg:grid-cols-2 lg:text-2xl">
                <div class="bg-zinc-25 space-y-6 border-black/5 p-10 max-lg:border-b lg:border-r">
                    <p>
                        Let's face it: every email platform you loved either doesn't support polls or charges you a
                        fortune for them.
                    </p>

                    <p>
                        Beehiiv wants you on their premium plan. HubSpot requires expensive upgrades. Ghost makes you
                        jump through hoops. Substack? Good luck finding polling features.
                    </p>

                    <p>Name one that makes polls easy and affordable. Thought so.</p>

                    <p><strong class="text-zinc-950">It's time to break free.</strong></p>
                </div>
                <div class="space-y-6 p-10">
                    <p>
                        <strong class="text-zinc-950">
                            Introducing AntiPoll — embed fully functional polls in ANY email service.
                        </strong>
                        Newsletters, marketing campaigns, automated sequences, it works everywhere.
                    </p>

                    <p>
                        Fast, platform-agnostic, and dead simple, it's a breath of fresh air in a world of email
                        platform lock-in.
                    </p>

                    <p>
                        We invite you to
                        <a href="/register" class="font-semibold text-zinc-950 underline" wire:navigate>
                            try AntiPoll and create 1000 polls for free
                        </a>
                        I'm excited you're here, and I can't wait to hear what you think.
                    </p>

                    <div class="flex items-start gap-4">
                        <flux:avatar size="lg" src="https://unavatar.io/x/oliverservinX" circle />

                        <div class="text-sm lg:text-base">
                            <p>
                                <strong class="text-zinc-950">
                                    Oliver Servín,
                                    <a href="mailto:oliver@antihq.com" class="font-semibold text-zinc-950 underline">
                                        oliver@antihq.com
                                    </a>
                                </strong>
                            </p>
                            <p><i>Founder of AntiHQ, makers of AntiPoll</i></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <flux:spacer class="my-32" />

    <section>
        <header>
            <h2 class="max-w-3xl text-4xl font-medium tracking-tighter text-pretty text-zinc-950 sm:text-6xl">
                Your email engagement engine.
            </h2>
            <p class="mt-6 max-w-3xl text-2xl font-medium text-zinc-500">
                We built everything you need to turn passive readers into active participants.
            </p>
        </header>

        <flux:spacer class="my-10 sm:my-16" />

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 max-lg:rounded-t-4xl lg:col-span-3 lg:rounded-l-4xl"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="h-80 bg-[url(../screenshots/create-poll.webp)] bg-size-[576px_527px] bg-position-[left_8px_top_-84px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-t from-white to-50%"></div>
                </div>
                <div class="relative p-10">
                    <p class="text-2xl/8 font-medium tracking-tight text-zinc-950">
                        Create polls in seconds
                    </p>
                    <p class="mt-2 max-w-[600px] text-sm/6 text-zinc-600">
                        Add questions, answers, and customize settings without any technical knowledge.
                    </p>
                </div>
            </div>
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 lg:col-span-3 lg:rounded-r-4xl"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="absolute inset-0 bg-[url(../screenshots/share-poll.webp)] bg-size-[576px_527px] bg-position-[left_8px_top_-20px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-t from-white to-50%"></div>
                </div>
                <div class="relative p-10">
                    <p class="text-2xl/8 font-medium tracking-tight text-zinc-950">
                        Works with every email platform
                    </p>
                    <p class="mt-2 max-w-[600px] text-sm/6 text-zinc-600">
                        Beehiiv, Ghost, HubSpot, Kit, Loops, MailerLite, Sendy, or even plain HTML emails. No platform
                        limitations, no expensive upgrades.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <flux:spacer class="my-32" />

    <section>
        <header>
            <h2 class="max-w-3xl text-4xl font-medium tracking-tighter text-pretty text-zinc-950 sm:text-6xl">
                Smart features, serious results.
            </h2>
            <p class="mt-6 max-w-3xl text-2xl font-medium text-zinc-500">
                <strong class="text-zinc-950">Auto-submit</strong>
                creates one-click polls.
                <strong class="text-zinc-950">Email collection</strong>
                builds your audience.
                <strong class="text-zinc-950">Custom redirects</strong>
                route users based on responses.
                <strong class="text-zinc-950">Feedback fields</strong>
                gather qualitative insights.
                <strong class="text-zinc-950">White-label options</strong>
                remove branding.
                <strong class="text-zinc-950">Real-time analytics</strong>
                track engagement instantly.
            </p>
        </header>

        <flux:spacer class="my-10 sm:my-16" />

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-6 lg:grid-rows-2">
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 max-lg:rounded-t-4xl lg:col-span-2 lg:rounded-tl-4xl"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="h-80 bg-[url(../screenshots/analytics.webp)] bg-size-[576px_527px] bg-position-[left_-200px_top_-180px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-b from-white to-50%"></div>
                </div>
            </div>
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 lg:col-span-2"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="absolute inset-0 bg-[url(../screenshots/embed.webp)] bg-size-[576px_527px] bg-position-[left_0px_top_-140px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-t from-white to-50%"></div>
                </div>
            </div>
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 lg:col-span-2 lg:rounded-tr-4xl"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="absolute inset-0 bg-[url(../screenshots/responses.webp)] bg-size-[576px_270px] bg-position-[left_16px_top_20px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-t from-white to-50%"></div>
                </div>
            </div>
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 lg:col-span-2 lg:rounded-bl-4xl"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="absolute inset-0 bg-[url(../screenshots/branding.webp)] bg-size-[576px_527px] bg-position-[left_0px_top_-130px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-b from-white to-50%"></div>
                </div>
            </div>
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 lg:col-span-2"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="absolute inset-0 bg-[url(../screenshots/mobile.webp)] bg-size-[576px_527px] bg-position-[left_0px_top_-73px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-t from-white to-50%"></div>
                </div>
            </div>
            <div
                class="group relative flex flex-col overflow-hidden rounded-lg bg-white shadow-xs ring-1 ring-black/5 lg:col-span-2 lg:rounded-br-4xl"
            >
                <div class="relative h-80 shrink-0">
                    <div
                        class="absolute inset-0 bg-[url(../screenshots/integrations.webp)] bg-size-[576px_527px] bg-position-[left_0px_top_-163px] bg-no-repeat"
                    ></div>
                    <div class="absolute inset-0 bg-linear-to-t from-white to-50%"></div>
                </div>
            </div>
        </div>
    </section>

    <flux:spacer class="my-32" />

    <section id="pricing">
        <header>
            <h2
                class="mx-auto max-w-3xl md:text-center text-4xl font-medium tracking-tighter text-pretty text-zinc-950 sm:text-6xl"
            >
                Create 1000 polls for free. No time limit, no user limit.
            </h2>
            <p class="mx-auto mt-6 max-w-3xl md:text-center text-2xl font-medium text-zinc-500">
                When you're ready for more, it's only $20/month for unlimited polls + all features. Cancel anytime, no
                risk.
            </p>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button href="/register" variant="primary" color="zinc" class="rounded-full! text-base!" wire:navigate>
                Start with 1000 free polls!
            </flux:button>
        </div>

        <flux:spacer class="my-6" />

        <p class="mx-auto max-w-xs text-center text-sm/6 text-zinc-500">
            No obligations, no credit card required, no time limit on the first 1000 polls.
        </p>
    </section>

    <flux:spacer class="my-32" />

    <section>
        <header>
            <h2
                class="mx-auto max-w-3xl md:text-center text-4xl font-medium tracking-tighter text-pretty text-zinc-950 sm:text-6xl"
            >
                And there's more... AntiPoll is open source and self-hostable.
            </h2>
            <p class="mx-auto mt-6 max-w-3xl md:text-center text-2xl font-medium text-zinc-500">
                Prefer to host it yourself or want to customize the code? You can run AntiPoll on your own server.
                <strong class="text-zinc-950">Want to contribute?</strong>
                <a href="https://github.com/antihq/poll/pulls" class="font-semibold text-zinc-950 underline" target="_blank">Submit a PR</a>
                to help improve the product for everyone.
                <a href="https://github.com/antihq/poll/blob/develop/LICENSE.md" class="font-semibold text-zinc-950 underline" target="_blank">Our license</a>
                permits flexible use with minimal restrictions.
            </p>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button
                href="https://github.com/antihq/poll"
                variant="primary"
                color="zinc"
                class="rounded-full! text-base!"
                target="_blank"
            >
                View source
            </flux:button>
        </div>
    </section>

    <flux:spacer class="my-32" />

    <section>
        <header>
            <h2 class="md:text-center text-4xl font-medium tracking-tighter text-pretty text-zinc-950 sm:text-6xl">
                Common questions.
            </h2>
        </header>

        <flux:spacer class="my-16" />

        <div class="mx-auto max-w-xl">
            <flux:accordion>
                <flux:accordion.item>
                    <flux:accordion.heading>Does AntiPoll require technical skills?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm/6 text-zinc-600">
                            No! If you can copy and paste HTML, you can use AntiPoll. We handle all the technical
                            complexity - you just create polls and embed them.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading>Can I really use AntiPoll with any email platform?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm/6 text-zinc-600">
                            Yes! AntiPoll generates universal HTML that works in any email service that supports basic
                            HTML. We also provide platform-specific templates for popular services with the correct
                            merge tags already included.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading>What happens to my poll data?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm/6 text-zinc-600">
                            You own all your poll data and responses. We don't lock you into our platform - you can
                            export your data at any time and switch services freely.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading>Do my subscribers need to create accounts?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm/6 text-zinc-600">
                            No! Your subscribers can respond to polls directly in their email client without creating
                            any accounts or visiting external websites (unless you enable redirects).
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading>
                        If I delete a poll, does it count against the 1000 polls?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm/6 text-zinc-600">
                            Yes, every poll you create is counted, even polls that are deleted later. AntiPoll tracks
                            the total number of polls created, so deleted polls still count against your 1000 poll
                            limit.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:spacer class="my-10" />

            <h3 class="md:text-center text-2xl/8 font-medium tracking-tight text-zinc-950">
                Need help? Check out our
                <a href="/docs" class="font-semibol text-zinc-950 underline" wire:navigate>documentation</a>
            </h3>
        </div>
    </section>

    <flux:spacer class="my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-950 sm:text-5xl">Ready to give AntiPoll a try?</h2>
        </header>

        <flux:spacer class="my-6" />

        <div class="flex justify-center">
            <flux:button href="/register" variant="primary" color="zinc" class="rounded-full! text-base!" wire:navigate>
                Get started now
            </flux:button>
        </div>
    </section>

    <flux:spacer class="my-32" />
</x-layouts::site>
