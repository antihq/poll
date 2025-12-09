# Done

- `/polls/1/share`
- Users can share a poll by selecting a platform (universal or kit) and copying it for pasting into their chosen provider
- The copied poll must include the question and possible answers. Each answer should be a link that preselects the response when clicked
- The poll in clipboard can be copy pasted in a html email body with the correct poll answer links
- The user should be able to choose the platform used to share the poll.
- The options are: Universal, Beehiiv, Brevo, EmailOctopus, Ghost, HubSpot, Kit, Loops, MailerLite, and Sendy.
- It uses the correct tag to link the response to subscribers/contacts depending on the selected platform.

# Notes

- Works universally with Apple Mail, Gmail, Substack and more, but it doesn't automatically link responses to subscribers or contacts.
- Ghost:
    - For email posts, it links responses to Ghost members. The poll must be embedded as an email call-to-action content card.
    - For regular blog posts, users must use the universal poll instead.
- HubSpot: It requires the "Marketing Hub Starter" plan or higher.
