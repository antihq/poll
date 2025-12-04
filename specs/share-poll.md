# Done

- `/polls/1/share`
- Users can share a poll by selecting a platform (universal or kit) and copying it for pasting into their chosen provider
- The copied poll must include the question and possible answers. Each answer should be a link that preselects the response when clicked
- The poll in clipboard can be copy pasted in a html email body with the correct poll answer links

# Todo

- User should be able to choose the platfmor used to share the poll
- Options are: Universal, Beehiiv, Brevo, EmailOctopus, Ghost, HubSpot, Kit, Loops, MailerLite, Sendy
- It uses the correct tag to link response to subscribers/contacts depending on the selected platform

# Notes

- Universal work for Apple Mail, Gmail, Substack and more but it dosent automatically link responses to subscribers/contacts
- Ghost:
	- On email posts. it liks responses to Ghost members. The poll must be embeded as an email call to action content card.
	- On regular blog posts. users must use the universal poll instead
- HubSpot: It requres "Marketing Hub Starter" plan or greater.