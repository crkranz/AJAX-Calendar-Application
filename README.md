# AJAX-Calendar-Application

### Overview: 
This project enhances a calendar application with advanced features to improve event organization, sharing, and collaboration among users. Key features include event tagging with filtering capabilities, color-coded events, calendar and group event sharing, and an intuitive user interface.

### Creative Features
- Event Tagging and Filtering
- Users can assign specific categories (tags) to events when creating or editing them.
- The calendar view allows users to toggle these tags on or off to filter events based on their categories.
- When a category is selected, only events matching the selected tag(s) are retrieved from the database and displayed on the calendar.

### Color-coded Events
- Events belonging to the same category share the same color, providing a visually appealing and organized way to distinguish between different event types.
- The color scheme is dynamically applied based on the event's category.

### Calendar Sharing
- Users can share their entire calendar with others for seamless collaboration.
- Clicking the Share Calendar button displays a dropdown populated with all available usernames (retrieved via a PHP function).
- When a calendar is shared, all events associated with it are simultaneously added to the recipient's database and displayed on their calendar.

### Group Event Sharing
- Users can share individual events with specific users to create group events.
- While creating a new event, an option allows users to specify another user to share the event with.
- Shared events automatically appear on the specified user's calendar and are added to their database.

### User Interface Design

- The user interface is sleek and intuitive, designed using clean CSS styling.

- Users can: Add events by clicking on a specific calendar cell, Edit or delete events by selecting an event and clicking the "Edit" button


### Event Management Features

- Event Tagging and Filtering: Users can filter calendar events by category.

- Calendar Sharing: Users can share their calendars with others for collaboration.

- Group Event Sharing: Users can share individual events with other users.


### Code Quality and Security

- Code Quality: Code is clean, well-documented, and properly formatted.

- Password Security: Passwords are securely stored using hashing and salting.

- AJAX Requests: Sensitive actions and modifications are performed using secure POST requests.

- XSS Prevention: Application is protected against XSS attacks by escaping all output content.

- SQL Injection Protection: SQL Injection attacks are prevented using proper query handling.

- CSRF Tokens: CSRF tokens are implemented for all actions.

- Session Security: Session cookies are HTTP-Only for added security.

- W3C Validation: Application passes W3C validation.


### Calendar View

- The calendar is displayed as a table grid with days as columns and weeks as rows.

- Users can navigate between months, both past and future, without limitations.


### Event Management

- Events can be added, edited, and deleted dynamically.

- Events include a title, date, and time.

- User Authentication ensures only logged-in users can manage events.

- Users cannot access or manipulate events belonging to others.

- Actions are performed seamlessly over AJAX without page reloads.

- User sessions persist across page reloads.


### Technologies Used

- Frontend: HTML, CSS, JavaScript, AJAX

- Backend: PHP

- Database: MySQL

- Security: CSRF tokens, hashed passwords, SQL Injection prevention

