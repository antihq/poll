## Done

- `/polls/create`
- Users can create a poll for their team by providing a name, a question, and answers.
- At least two answers are required.
- Users can add new answers or delete them.
- After creation, user is redirected to edit page `/polls/1/edit`.
- Each answer can be drag and dropped to change order
- Shows response usage: "X/5000 responses used" for unsubscribed teams (based on responses received)
- Teams at 5000 responses are redirected to upgrade page
- Poll creation is blocked when team reaches 5000 responses (unsubscribed)
- Response count is cumulative - responses from deleted polls still count against 5000 response limit
