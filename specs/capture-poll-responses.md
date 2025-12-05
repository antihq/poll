## Done

- `/p/{poll:ulid}`
- Guests can view polls
- Guests select their poll response and submit it
- Display a thank you message after they submit their response
- Answers can be preselected via a URL query
- Email is autofilled via a URL query
- If a poll answer has a redirect URL configured, redirect the user to the redirect URL
- If a poll answer has a feedback field enabled, show the feedback field and capture the feedback answer
- If the poll is configured to no longer accept responses, it should not be visible
- If the poll is configured to require an email address, a field to capture the guest's email should be shown to capture the email. The email field now needs to be required
- If the poll is configured to auto-submit the vote, the user does not need to submit the response; it should use the answer and email parameter to auto-submit the vote
- If the poll is configured to call for feedback, a text field should be shown after submitting the response to capture the feedback
- If the poll is configured with a custom thank you message after submission, display it instead of the hardcoded one
- If the poll is configured to show a button on the thank-you screen, display it, using the configured button label and button URL
- If the poll is configured to hide branding, do not show the Antipoll branding on the poll
- Poll layout is configurable; it can be vertical or horizontal
