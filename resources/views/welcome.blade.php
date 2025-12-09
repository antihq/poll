<x-layouts::site>
    <flux:spacer class="my-20 lg:my-32" />

    <div class="text-center">
        <h1 class="mx-auto max-w-4xl text-5xl font-medium tracking-tight text-zinc-900 sm:text-7xl">
            Polls in emails.
            <br />
            Not platform upgrades.
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-lg tracking-tight text-zinc-700">
            Start your 15-day free trial.
            <a href="#pricing" class="font-medium text-[var(--color-accent-content)] underline">Only $5/month after that.</a>
        </p>

        {{-- <img src="dashboard.webp" class="mt-10 rounded-lg" /> --}}

        <flux:spacer class="my-36 lg:my-44" />

        <div class="overflow-hidden rounded-4xl border border-zinc-200 shadow-xl">
            <div class="grid grid-cols-2 text-left text-lg tracking-tight text-zinc-700">
                <div class="bg-zinc-25 space-y-4 border-r border-zinc-100 px-14 py-16 xl:px-16">
                    <p>
                        Let's be honest: every email platform you loved either doesn't support polls or charges you a
                        fortune for them.
                    </p>

                    <p>
                        Beehiiv wants you on their premium plan. HubSpot requires expensive upgrades. Ghost makes you
                        jump through hoops. Substack? Good luck finding polling features.
                    </p>

                    <p>Name one that makes polls easy and affordable. Exactly.</p>

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
                        <strong class="font-medium text-zinc-900">
                            <a href="/" class="underline">try AntiPoll with a 15-day free trial</a>
                        </strong>
                        . I'm glad you're here, and I'd love to hear what you think.
                    </p>

                    <div>
                        <p>
                            <strong class="font-medium text-zinc-900">
                                Oliver Servín,
                                <a href="/" class="underline">oliver@antihq.com</a>
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
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">
                Your email engagement multiplier.
            </h2>
            <p class="mt-4 text-lg tracking-tight text-zinc-700">
                We put together everything you need to turn passive readers into active participants.
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
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">Little features, big impact.</h2>
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
                15-day free trial. No platform restrictions.
            </h2>
            <p class="mx-auto mt-4 max-w-xl text-lg tracking-tight text-zinc-700">
                After your trial, it's only $5/month for unlimited polls and all features. Cancel anytime, no risk.
            </p>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button href="/" variant="primary" class="font-semibold">Start my 15-day free trial!</flux:button>
        </div>

        <flux:spacer class="my-6" />

        <div class="flex justify-center">
            <ul class="flex flex-col gap-y-3 text-sm">
                <li class="flex gap-4">
                    <flux:icon.check variant="mini" color="green" />
                    Full analytics and all features included from day one.
                </li>
                <li class="flex gap-4">
                    <flux:icon.check variant="mini" color="green" />
                    Team management and white-label options included.
                </li>
                <li class="flex gap-4">
                    <flux:icon.check variant="mini" color="green" />
                    Cancel anytime easily from your account dashboard.
                </li>
            </ul>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">
                One more thing... AntiPoll works everywhere.
            </h2>
            <p class="mx-auto mt-4 max-w-4xl text-lg tracking-tight text-zinc-700">
                Unlike platform-specific solutions that lock you into one ecosystem, AntiPoll generates universal HTML
                that works in any email client.
                <strong class="font-medium text-zinc-900">Have a favorite email platform?</strong>
                AntiPoll probably works with it already. Our approach ensures you own your audience data, not your email
                platform.
            </p>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button href="/" variant="primary" class="font-semibold">View all integrations</flux:button>
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
                            No! If you can copy and paste HTML, you can use AntiPoll. We handle all the technical complexity
                            - you just create polls and embed them.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">
                        Can I really use AntiPoll with any email platform?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            Yes! AntiPoll generates universal HTML that works in any email service that supports basic HTML.
                            We also provide platform-specific templates for popular services with the correct merge tags
                            already included.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">What happens to my poll data?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            You own all your poll data and responses. We don't lock you into our platform - you can export
                            your data at any time and switch services freely.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">
                        Do my subscribers need to create accounts?
                    </flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            No! Your subscribers can respond to polls directly in their email client without creating any
                            accounts or visiting external websites (unless you enable redirects).
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
                <flux:accordion.item>
                    <flux:accordion.heading class="text-lg/7!">Any AI features?</flux:accordion.heading>
                    <flux:accordion.content>
                        <p class="text-sm text-zinc-700">
                            We didn't add artificial intelligence, we just removed all the friction from email polling. No
                            platform lock-in, no expensive upgrades, plenty of power, and dead-simple implementation. Try
                            it.
                        </p>
                    </flux:accordion.content>
                </flux:accordion.item>
            </flux:accordion>

            <flux:spacer class="my-8" />

            <h3 class="text-lg/7! font-medium md:text-center">
                Need help? Check out our
                <a href="/docs" class="underline font-medium">documentation</a>
            </h3>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />

    <section>
        <header class="md:text-center">
            <h2 class="text-3xl font-medium tracking-tight text-zinc-900 sm:text-4xl">
                Yeah, why not, I'll try AntiPoll!
            </h2>
        </header>

        <flux:spacer class="my-10" />

        <div class="flex justify-center">
            <flux:button href="/" variant="primary" class="font-semibold">Sign up now</flux:button>
        </div>
    </section>

    <flux:spacer class="my-20 lg:my-32" />
</x-layouts::site>
