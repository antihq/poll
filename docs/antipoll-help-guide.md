# AntiPoll Help Guide

## Welcome to AntiPoll

AntiPoll is the ultimate solution for embedding polls in newsletters and emails. Many email platforms don't support interactive polls, or they require expensive plan upgrades to access polling features. AntiPoll solves this by letting you embed fully functional polls in any email service - no upgrades required.

Whether you're using Beehiiv, Ghost, HubSpot, or any other platform, AntiPoll makes it easy to collect feedback from your audience without platform limitations.

Now, let's get started!

## Getting Started

### Dashboard

After logging in, you'll land on the Dashboard where you can see all your polls at a glance. The dashboard shows your poll list with response counts and creation dates, making it easy to track engagement across all your surveys.

![Dashboard with poll list](https://example.com/dashboard.png)

From the dashboard, you can quickly create new polls, view existing ones, and monitor response activity. Each poll displays its name, response count, and creation date for easy reference.

### Navigation

Getting around AntiPoll is simple and intuitive. Use the main navigation to access different sections:

- **Dashboard** - View all your polls
- **Polls** - Create and manage your surveys
- **Settings** - Manage your profile and team settings

The interface is clean and focused, helping you get to what you need without unnecessary clicks or complexity.

## Creating Polls

### Add a New Poll

Creating polls is straightforward and flexible. From the dashboard or polls page, click **New poll** to get started.

![Add poll button](https://example.com/add-poll-button.png)

On the create poll page, you'll need to provide:

1. **Poll name** - A descriptive title for your survey
2. **Question** - The main question you want to ask
3. **Answers** - At least two answer options

![Poll creation form](https://example.com/poll-creation.png)

#### Managing Answers

- **Add answers** - Click "Add answer" to create more options
- **Remove answers** - Use the delete button (minimum 2 answers required)
- **Reorder answers** - Drag and drop answers to change their order

Each answer can be customized with individual settings like redirect URLs and feedback fields.

### Poll Settings

After creating a poll, you can configure advanced settings to match your specific needs:

#### Response Settings

- **Accept responses** - Toggle whether the poll is currently active
- **Require email address** - Make email collection mandatory
- **Auto-submit responses** - Automatically submit when an answer is selected
- **Collect feedback** - Show a feedback field after submission

#### Display Settings

- **Layout** - Choose between vertical or horizontal answer arrangement

#### After Submission

Configure what happens after someone responds:

- **Show thank you message** - Display a custom message with optional button
- **Redirect to URL** - Automatically redirect users to a custom URL

#### Branding

- **Hide AntiPoll branding** - Remove AntiPoll branding for a white-label experience

## Managing Polls

### View Poll List

The polls page shows all your surveys in a clean table format. Each poll displays:

- Poll name with avatar
- Response count badge
- Creation date

![Poll list table](https://example.com/poll-list.png)

### View Poll Details

Click on any poll to see detailed results and analytics:

- **Total responses** - Overall response count
- **Answer breakdown** - Percentage and count for each answer option
- **Visual progress bars** - Easy-to-read response distribution

![Poll results with charts](https://example.com/poll-results.png)

### Edit Polls

Need to make changes? You can edit:

- Poll name and question
- Answer options and their order
- Individual answer settings

Access edit options from the poll details page menu.

### Answer Settings

Each answer can have its own custom settings:

#### Redirect URLs

- Enable custom redirects for specific answer choices
- Perfect for routing users to different pages based on their response

#### Feedback Fields

- Show feedback prompts for particular answers
- Customize the feedback field label
- Collect detailed qualitative responses

## Sharing Polls

### Share Your Poll

This is where AntiPoll shines! Most email platforms either don't support polls or charge extra for them. AntiPoll lets you embed interactive polls in ANY email service.

From the share page, you can:

1. **Select your platform** - Choose from Universal, Beehiiv, Brevo, EmailOctopus, Ghost, HubSpot, Kit, Loops, MailerLite, or Sendy
2. **Copy HTML** - Get platform-specific HTML with proper email merge tags
3. **Preview** - See how your poll will look before sharing

![Share interface with platform selection](https://example.com/share-interface.png)

#### Why This Matters

- **No platform limitations** - Works even if your email service doesn't support polls
- **No expensive upgrades** - Skip the premium plan requirements
- **Universal compatibility** - Works with any email service that supports HTML
- **Subscriber tracking** - Automatically links responses to your email list

#### Platform Integration

- **Universal** - Works with any email platform including Apple Mail, Gmail, Substack, and more
- **Email platforms** - Automatically links responses to subscribers/contacts for better tracking
- **Ghost** - Special integration for email posts and blog content
- **HubSpot** - Works with any HubSpot plan (no expensive upgrades required)

The copied HTML includes your poll question and clickable answer links, ready to paste into email campaigns or newsletters.

## Viewing Responses

### Response Analytics

Track your poll performance with comprehensive analytics:

- **Response counts** - Total and per-answer statistics
- **Percentage breakdowns** - Visual representation of answer distribution
- **Response details** - Individual response data with timestamps

### Answer Details

Drill down into specific answers to see:

- **Respondent emails** - When email collection is enabled
- **Feedback responses** - Qualitative feedback from respondents
- **Response timestamps** - When each response was submitted

![Answer details with feedback](https://example.com/answer-details.png)

## Advanced Features

### Auto-Submit

Enable auto-submit to create seamless one-click polls:

- Perfect for quick feedback collection
- Reduces friction for respondents
- Ideal for email campaigns where engagement is key

### Email Collection

Collect email addresses to:

- Build your subscriber list
- Follow up with respondents
- Segment your audience based on responses

### Custom Branding

Remove AntiPoll branding to:

- Maintain brand consistency
- Create white-label experiences
- Use polls in professional contexts

## Settings & Management

### Profile Settings

Manage your account information:

- Update your name and email
- Upload profile avatar
- Configure appearance preferences

### Team Management

For team accounts:

- Invite team members
- Manage permissions
- Switch between teams

### Poll Management

Full control over your polls:

- Edit poll details at any time
- Delete polls when no longer needed
- Configure individual answer settings

## Best Practices

### Poll Design

- **Keep questions clear and concise** - Avoid ambiguity
- **Limit answer options** - Too many choices can overwhelm respondents
- **Use descriptive poll names** - Help yourself stay organized

### Sharing Strategy

- **Choose the right platform** - Match your email service provider
- **Test before sending** - Preview your poll in different email clients
- **Consider mobile users** - Ensure polls work well on all devices

### Response Collection

- **Enable email collection** - Build your audience while gathering feedback
- **Use feedback fields** - Collect qualitative insights alongside quantitative data
- **Monitor responses** - Stay engaged with your audience's feedback

## Technical Resources

### Platform Integration

AntiPoll integrates seamlessly with major email platforms:

- **Beehiiv** - `{{email}}` merge tag
- **Brevo** - `{{contact.EMAIL}}` merge tag
- **EmailOctopus** - `{{EmailAddress}}` merge tag
- **Ghost** - `{email}` merge tag
- **HubSpot** - `{{personalization_token('contact.email','')}}` merge tag
- **Kit** - `{{ subscriber.email_address }}` merge tag
- **Loops** - `{email}` merge tag
- **MailerLite** - `{email}` merge tag
- **Sendy** - `[Email]` merge tag

### URL Parameters

Enhance your polls with URL parameters:

- `?answer={ulid}` - Preselect an answer
- `?email={email}` - Autofill email address

## Help & Support

### FAQ

**Q: Can I change the order of answers after creating a poll?**

**A:** Yes! You can drag and drop answers to reorder them in both the create and edit screens.

---

**Q: What happens if I delete a poll?**

**A:** Deleting a poll permanently removes all associated answers and responses. This action cannot be undone.

---

**Q: Can I export poll responses?**

**A:** Currently, you can view response details in the interface. Export functionality may be added in future updates.

---

**Q: How many polls can I create?**

**A:** Unlimited! AntiPoll includes unlimited poll creation with your subscription - no limits or restrictions.

---

**Q: Can I customize the poll appearance?**

**A:** Yes! You can choose between vertical and horizontal layouts, and hide AntiPoll branding for a white-label experience. All customization features are included with your subscription.

### Contact Us

For additional support or questions, please contact the AntiPoll team.
