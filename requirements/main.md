## Here the Specifications for the Quote and Project Management Program

**1\. Quote Management**

- Creation and management of quotes with the following fields:

    - Quote number
    - Project type
    - Client information (name, contact, address, phone, email)
    - Quote language (English or French)
    - Project address
    - Budget
    - Sales representative name
    - Quote status (in preparation, sent, approved, rejected)
    - has one project manager (responsible)
    - A has a unique client

- Ability to create a project from an existing quote

**2\. Project Management**

- Creation and tracking of projects with the following fields:

    - Project number
    - Project type
    - Department
    - Project manager
    - Client information (name, contact, address, phone, email)
    - Project language
    - Project address
    - Budget
    - Sales representative name
    - Project status (ongoing, completed, canceled)
    - Project opening date
    - Deadline
    - Project invoiced (invoice number and amount)
    - Paid invoice (paid invoice number)

**3\. Client Management**

- Creation and management of clients with the following fields:

    - Company name
    - Contact person
    - Address
    - Phone
    - Email
    - Quotes (many)
    - Projects (many)

**4\. Employee Time Sheets**

- Employee work time entry with the following fields:

    - Employee
    - Task performed
    - Associated project number
    - Worked duration

**5\. Employee Management**

- Module to manage employees with the following fields:

    - Username
    - Password
    - First name
    - Last name
    - Department
    - Entry date in the company

**6\. Client Portal**

- Interface allowing clients to:

    - Submit quotes
    - View reports

**7\. Workload Management**

- Visualization of employee workload
- Display of ongoing projects and deadlines
- Representation in Gantt chart

**8\. Views**

- Visualization of the projects that are completed but not invoiced
- Visualization of the projects that are invoiced but not paid
- Visualization of the projects of each department (ongoing projects) (also possibility to choose by month or by year)
- Visualization of the budget or amount of invoiced projects (by type of projects, by department, by month, by year…)
- Visualization of the time sheets of each employee for payroll with approval by leads

**9\. Mass Emailing**

- Access to the client list
- Ability for a sales representative to send mass emails to multiple clients

**10\. Search Engine**

- Functionality to search:

    - A project
    - A quote
    - A client

**11\. User Roles**

- Admin : can access and edit everything and can approve time sheets
- Receptionist : can access and edit everything but cannot approve time sheets
- Employee : can only access Projects, Clients, Quotes and can edit timesheet and space reserved to clients (they will upload files there)
- Accountant : can only access Projects, Clients, Quotes, space reserved to clients and can edit timesheet
- Client : can only access their space reserved and download files

## Additional info:

- DB is MySQL
- authentication is already built. Do not work o it.
- Please use soft deletes.

## Relationships with Cardinality:

Quote --> Project : one-to-one
Client --> Quote : one-to-one
Client --> Project : one-to-many
Employee --> TimeSheet : one-to-many
Project --> TimeSheet : one-to-one
Employee --> "0..\*" Project : one-to-many
Employee --> "1" Workload : one-to-one
Workload --> Project : one-to-many
Project --> Invoice : one-to-many
