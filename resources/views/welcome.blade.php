<x-layouts::site>
    <flux:spacer class="my-20 lg:my-32" />

    <div class="text-center">
        <h1 class="mx-auto max-w-4xl text-5xl font-medium tracking-tight text-zinc-900 sm:text-7xl">
            Polls in emails.
            <br />
            Not platform lock-in.
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-lg tracking-tight text-zinc-700">
            Create 1000 polls FREE with AntiPoll. After that it's
            <a href="#pricing" class="font-medium text-zinc-950 underline">only $20/month.</a>
        </p>

        {{-- <img src="dashboard.webp" class="mt-10 rounded-lg" /> --}}

        <flux:spacer class="my-36 lg:my-44" />

        <div class="overflow-hidden rounded-4xl border border-zinc-200 shadow-xl">
            <div class="grid grid-cols-2 text-left text-lg tracking-tight text-zinc-700">
                <div class="bg-zinc-25 space-y-4 border-r border-zinc-100 px-14 py-16 xl:px-16">
                    <p>
                        Let's face it: every email platform you loved either doesn't support polls or charges you a
                        fortune for them.
                    </p>

                    <p>
                        Beehiiv wants you on their premium plan. HubSpot requires expensive upgrades. Ghost makes you
                        jump through hoops. Substack? Good luck finding polling features.
                    </p>

                    <p>Name one that makes polls easy and affordable. Thought so.</p>

                    <p><strong class="font-medium text-zinc-900">It's time to break free.</strong></p>
                </div>
                <div class="space-y-4 bg-white px-14 py-16 xl:px-16">
                    <p>
                        <strong class="font-medium text-zinc-900">
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
                        <a href="/" class="font-medium text-zinc-950 underline">
                            try AntiPoll and create 1000 polls for free
                        </a>
                        I'm excited you're here, and I can't wait to hear what you think.
                    </p>

                    <div class="text-base">
                        <p>
                            <strong class="font-medium text-zinc-900">
                                Oliver Servín,
                                <a href="mailto:oliver@antihq.com" class="text-zinc-950 underline">oliver@antihq.com</a>
                            </strong>
                        </p>
                        <p><i>Founder of AntiHQ, makers of AntiPoll</i></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="mx-auto max-w-xl md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">Your email engagement engine.</h2>
            <p class="mt-4 text-lg tracking-tight text-zinc-700">
                We built everything you need to turn passive readers into active participants.
            </p>
        </header>

        <flux:spacer class="my-16" />

        <div>
            <div class="grid grid-cols-2 gap-x-8">
                <p class="mt-4 text-sm text-zinc-600">
                    Create polls in
                    <strong class="font-medium text-zinc-900">seconds</strong>
                    with our intuitive interface. Add questions, answers, and customize settings without any technical
                    knowledge.
                </p>
                <p class="mt-4 text-sm text-zinc-600">
                    <strong class="font-medium text-zinc-900">Works with every email platform</strong>
                    - Beehiiv, Ghost, HubSpot, Kit, Loops, MailerLite, Sendy, or even plain HTML emails. No platform
                    limitations, no expensive upgrades.
                </p>
            </div>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">
                Smart features, serious results.
            </h2>
            <p class="mx-auto mt-4 max-w-3xl text-lg tracking-tight text-zinc-700">
                <strong class="text-zinc-900">Auto-submit</strong>
                creates one-click polls.
                <strong class="text-zinc-900">Email collection</strong>
                builds your audience.
                <strong class="text-zinc-900">Custom redirects</strong>
                route users based on responses.
                <strong class="text-zinc-900">Feedback fields</strong>
                gather qualitative insights.
                <strong class="text-zinc-900">White-label options</strong>
                remove branding.
                <strong class="text-zinc-900">Real-time analytics</strong>
                track engagement instantly.
            </p>
        </header>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section id="pricing">
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">
                Create 1000 polls for free. No time limit, no user limit.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg tracking-tight text-zinc-700">
                When you're ready for more, it's only $20/month for unlimited polls + all features. Cancel anytime, no
                risk.
            </p>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button
                href="/"
                variant="primary"
                color="zinc"
                icon:trailing="arrow-right-circle"
                class="font-semibold"
            >
                Start with 1000 free polls!
            </flux:button>
        </div>

        <flux:spacer class="my-6" />

        <div class="flex justify-center">
            <ul class="flex flex-col gap-y-3 text-sm">
                <li class="flex gap-4">
                    <flux:icon.check-circle variant="mini" color="green" />
                    No obligations, no credit card required, no time limit on the first 1000 polls.
                </li>
                <li class="flex gap-4">
                    <flux:icon.check-circle variant="mini" color="green" />
                    Full analytics and all features included from day one.
                </li>
                <li class="flex gap-4">
                    <flux:icon.check-circle variant="mini" color="green" />
                    Team management and white-label options included.
                </li>
                <li class="flex gap-4">
                    <flux:icon.check-circle variant="mini" color="green" />
                    Cancel anytime easily from your account dashboard.
                </li>
            </ul>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">
                And there's more... AntiPoll is open source and self-hostable.
            </h2>
            <p class="mx-auto mt-4 max-w-4xl text-lg tracking-tight text-zinc-700">
                Prefer to host it yourself or want to customize the code? You can run AntiPoll on your own server.
                <strong class="font-medium text-zinc-900">Want to contribute?</strong>
                Submit a PR to help improve the product for everyone. Our license permits flexible use with minimal
                restrictions.
            </p>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button
                href="https://github.com/antihq/poll"
                variant="primary"
                color="zinc"
                icon:trailing="arrow-right-circle"
                class="font-semibold"
            >
                View source
            </flux:button>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">Common questions.</h2>
        </header>

        <flux:spacer class="my-10" />

        <div class="mx-auto max-w-2xl">
            <flux:accordion>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">
                        Does AntiPoll require technical skills?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            No! If you can copy and paste HTML, you can use AntiPoll. We handle all the technical
                            complexity - you just create polls and embed them.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">
                        Can I really use AntiPoll with any email platform?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            Yes! AntiPoll generates universal HTML that works in any email service that supports basic
                            HTML. We also provide platform-specific templates for popular services with the correct
                            merge tags already included.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">What happens to my poll data?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            You own all your poll data and responses. We don't lock you into our platform - you can
                            export your data at any time and switch services freely.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">
                        Do my subscribers need to create accounts?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            No! Your subscribers can respond to polls directly in their email client without creating
                            any accounts or visiting external websites (unless you enable redirects).
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">
                        If I delete a poll, does it count against the 1000 polls?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            Yes, every poll you create is counted, even polls that are deleted later. AntiPoll tracks
                            the total number of polls created, so deleted polls still count against your 1000 poll
                            limit.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:spacer class="my-8" />

            <h3 class="text-lg/7! font-medium md:text-center">
                Need help? Check out our
                <a href="/docs" class="font-medium text-zinc-950 underline">documentation</a>
            </h3>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">Ready to give AntiPoll a try?</h2>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button
                href="/"
                variant="primary"
                color="zinc"
                icon:trailing="arrow-right-circle"
                class="font-semibold"
            >
                Get started now
            </flux:button>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />
</x-layouts::site>
