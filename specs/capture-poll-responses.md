## Done

- `/p/{poll:ulid}`
- Guests can view polls
- Guests select their poll response and submit it
- Display a thank you message after they submit their response
- Answers can be preselected via a URL query
- Email is autofilled via a URL query

## Todo

- if a poll answer has a redirec url configured, redirect the user to the redirec url
- if a poll answer has a feedback field enabled, show the feedback field and capture the feedback answer
- if poll is configured to no loger accept response, the shoould not be visible
- if poll is configured to require an email address, a field to capture guest email should be shown to capture the email. The email field now need to be required
- if poll is configured to auto submit the vote, then the user no need to submit the response, it shuold use the answer and email parameter to auto submite the vote
- if poll is configured to called feedback, then a text field should be shown after submitting the response and capture the feedback
- if poll is configured with a custom thank you message after submission, display it instead of the hardcoded one
- if poll is configured to show a button on the thanyou screen, then display it, it uses the configured button label and button url
- if poll is configured to hide branding, then dont show the antipoll branding on the poll