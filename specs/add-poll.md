## Done

- `/polls/create`
- Users can create a poll for their team by providing a name, a question, and answers.
- At least two answers are required.
- Users can add new answers or delete them.
- After creation, user is redirected to edit page `/polls/1/edit`.
- Each answer can be drag and dropped to change order
- Shows poll usage: "X/1000 polls used" for unsubscribed teams (based on cumulative count)
- Teams at 1000 polls are redirected to upgrade page
- Poll creation is blocked when team reaches 1000 polls (unsubscribed)
- Poll count is cumulative - deleted polls still count against the 1000 poll limit
