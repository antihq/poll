# Done

- `/subscription-required`
- Shows message: "Your team has reached the free tier limit of 1000 polls."
- Allows team switching to check other teams' subscription status
- Team owners can proceed to checkout for subscription
- Users can log out
- Redirects to dashboard if team already has subscription

# Notes

- Teams are redirected here only when they try to create new polls after creating 1000+ polls without subscription
- All other poll features (viewing, editing, sharing, configuring) remain accessible
- Each team has separate 1000 poll limit
- Deleted polls still count against quota (cumulative counting system)
- The limit is configurable in `config/poll.php`
- Poll count tracking uses cumulative `polls_created` column to ensure deleted polls count towards limit
