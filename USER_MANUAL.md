# University Management System — User Manual

**Version**: 1.0  
**Platform**: Laravel 12 / PHP 8.3  
**Last Updated**: June 2026

---

## Table of Contents

1. [Getting Started](#1-getting-started)
2. [Dashboard](#2-dashboard)
3. [Student Management](#3-student-management)
4. [Admissions](#4-admissions)
5. [Academic Structure](#5-academic-structure)
6. [Academic Calendar](#6-academic-calendar)
7. [Course Offerings](#7-course-offerings)
8. [Course Registration](#8-course-registration)
9. [Timetable](#9-timetable)
10. [Attendance](#10-attendance)
11. [Examinations](#11-examinations)
12. [Grades & Results](#12-grades--results)
13. [Grade Appeals](#13-grade-appeals)
14. [Student Holds](#14-student-holds)
15. [Graduation](#15-graduation)
16. [E-Learning](#16-e-learning)
17. [Finance — Fee Structures](#17-finance--fee-structures)
18. [Finance — Billing](#18-finance--billing)
19. [Finance — Payments](#19-finance--payments)
20. [Finance — Scholarships](#20-finance--scholarships)
21. [Finance — Reports](#21-finance--reports)
22. [Department Budgets](#22-department-budgets)
23. [HR — Employees](#23-hr--employees)
24. [HR — Leave Management](#24-hr--leave-management)
25. [HR — Payroll](#25-hr--payroll)
26. [HR — Salary Advances](#26-hr--salary-advances)
27. [Library](#27-library)
28. [Hostel Management](#28-hostel-management)
29. [Asset Management](#29-asset-management)
30. [Research Management](#30-research-management)
31. [Announcements](#31-announcements)
32. [Messaging](#32-messaging)
33. [Notifications](#33-notifications)
34. [Documents](#34-documents)
35. [Support Tickets](#35-support-tickets)
36. [Alumni Management](#36-alumni-management)
37. [Reports](#37-reports)
38. [Admin Settings](#38-admin-settings)
39. [Role Reference](#39-role-reference)

---

## 1. Getting Started

### Logging In

1. Navigate to the system URL in your browser.
2. Enter your **email address** and **password**.
3. Click **Login**.
4. You will be redirected to your role-specific dashboard.

### Forgot Password

1. On the login page, click **Forgot Password**.
2. Enter your registered email address.
3. Check your email for a reset link.
4. Click the link, enter a new password, and confirm.

### Your Profile

Access your profile by clicking your name or avatar in the top-right corner.

- **Update your name, phone, and address** on the profile page.
- **Change your password** using the password tab.
- **Upload a profile photo** using the avatar section.

---

## 2. Dashboard

The dashboard is the first screen after login. It automatically shows information relevant to your role.

| Role | What You See |
|------|-------------|
| Student | My courses, GPA, upcoming exams, recent results, attendance summary |
| Registrar | Enrolment stats, admissions, registration counts, program breakdown |
| Lecturer | Assigned courses, attendance summary, upcoming exams, recent results |
| Finance Officer | Revenue collected, outstanding balances, recent payments |
| HR Officer | Employee headcount, pending leaves, payroll summary |
| Librarian | Books issued, overdue returns, fines collected |
| Hostel Manager | Occupancy rate, vacant rooms, recent check-ins/outs |
| Super Admin | Full system overview — all modules |

The dashboard refreshes automatically. Use the sidebar to navigate to any module.

---

## 3. Student Management

**Who can use this**: Registrar, Super Admin

### Viewing Students

1. Go to **Students** in the sidebar.
2. Use the search bar or filters (program, status, year) to find students.
3. Click a student's name to open their full profile.

The profile shows:
- Personal and contact information
- Enrolled program, level, and GPA
- Course registrations (Courses tab)
- Academic results (Results tab)
- Guardian information (Guardian tab)
- Account holds (Holds tab — admin/registrar only)
- Fee payment status

### Adding a Student

1. Click **New Student**.
2. Fill in:
   - Personal details (name, gender, date of birth, nationality, ID)
   - Contact information (phone, address)
   - Program and year of study
   - Admission type and enrollment date
3. A linked login account is created automatically using the email provided.
4. Click **Save**.

### Editing a Student

1. Open the student's profile.
2. Click **Edit** in the top-right.
3. Update any fields and click **Update Student**.

### Student Status

From the student profile, use the **Status** dropdown to:
- **Reinstate** — make active again
- **Suspend** — temporarily restrict access
- **Defer Enrollment** — pause studies
- **Graduate** — mark as completed
- **Withdraw** — permanent removal

### Student ID Card

1. Open the student's profile.
2. Click **ID Card** to view a printable card.
3. Use your browser's print function (Ctrl+P) to print.

For bulk card printing: go to the student list, select students, and click **Bulk Cards**.

### Academic Transcript

1. Open the student's profile.
2. Click **Transcript**.
3. A printable transcript with all results is generated.

### Bulk Import

1. Click **Import Template** to download the Excel template.
2. Fill in student data following the column headers.
3. Click **Bulk Import** and upload your file.

---

## 4. Admissions

**Who can use this**: Public (apply), Registrar, Super Admin (manage)

### Public Application (for Applicants)

1. Visit the public landing page and click **Apply Now**.
2. Fill in personal information, program choice, and academic background.
3. Submit the form.
4. You will receive a confirmation with your application reference number.

### Managing Applications (Staff)

1. Go to **Admissions** in the sidebar.
2. View a list of all applications with their statuses (Pending / Approved / Rejected).

### Reviewing an Application

1. Click an applicant's name.
2. Review their details.
3. Click **Approve** to admit them — this creates a Student account automatically.
4. Click **Reject** to decline — optionally add a rejection reason.

### Generating an Admission Letter

1. Open an approved application.
2. Click **Admission Letter** to generate a printable offer letter.

### Creating a Manual Admission (Staff)

If a student applies in person:
1. Click **New Admission**.
2. Fill in all required fields.
3. Save and then approve the record.

---

## 5. Academic Structure

**Who can use this**: Registrar, Super Admin

The academic structure is a hierarchy: **Faculty → Department → Program → Course**.

### Faculties

1. Go to **Academic → Faculties**.
2. Click **New Faculty** to add one (name, code, dean).
3. Click a faculty to see its departments and programs.

### Departments

1. Go to **Academic → Departments**.
2. Click **New Department** — select the parent faculty.
3. The department show page lists all programs and staff.

### Programs

1. Go to **Academic → Programs**.
2. Click **New Program** — select the parent department, set duration, credit hours required, and level (Certificate, Diploma, Degree, Masters, PhD).
3. The program show page lists enrolled students and courses.

### Courses

1. Go to **Academic → Courses**.
2. Click **New Course** — enter code, name, credits, level, type (theory/lab/practical), and assign to a department.
3. On the course detail page, you can manage **Prerequisites**:
   - Click **Add** to expand the prerequisites form.
   - Select the prerequisite course and minimum grade.
   - Click **Save**.
   - Remove prerequisites using the trash icon.

---

## 6. Academic Calendar

**Who can use this**: Registrar, Super Admin

### Academic Years

1. Go to **Academic → Academic Years**.
2. Click **New Academic Year** — enter the name (e.g., 2024/2025) and date range.
3. Click **Set Current** on the year that is currently active.

### Semesters

1. Go to **Academic → Semesters**.
2. Click **New Semester** — select the academic year, name (e.g., Semester 1), and date range.
3. Click **Set Current** to mark the active semester.

> **Important**: Always set the correct current semester before registrations, attendance, and results begin.

---

## 7. Course Offerings

**Who can use this**: Registrar, Super Admin

A course offering links a course to a specific semester with a lecturer and a student capacity.

### Creating an Offering

1. Go to **Academic → Course Offerings**.
2. Click **New Offering**.
3. Select:
   - Course
   - Academic Year and Semester
   - Lecturer (staff member)
   - Venue
   - Maximum students
4. Save.

### Managing an Offering

Click an offering to see:
- Enrolled students
- Timetable slots
- Examinations
- Results

---

## 8. Course Registration

**Who can use this**: Students (register own courses), Registrar (manage all)

### Student Registration

1. Go to **My Courses** in the sidebar.
2. Registered courses are shown in the main table with status badges.
3. **Waitlisted courses** appear in a yellow panel above — showing your queue position.

#### Registering for Courses

1. On the **My Courses** page, the right panel shows **Available Courses**.
2. Tick the checkboxes next to courses you want to register.
3. Click **Register Selected**.

The system will:
- Check you have no **active holds** blocking registration.
- Check you have completed all **prerequisites** for each course.
- Register you immediately if a seat is available.
- Add you to the **waitlist** if the course is full.

#### Dropping a Course

1. On the **My Courses** page, click **Drop** next to a course.
2. Confirm the action.

When you drop a course, the next student on the waitlist is automatically notified.

#### Confirming a Waitlist Spot

When a seat opens up, you will receive a notification. Return to **My Courses** and click **Confirm Enrollment** within the waitlist panel before the spot expires.

### Registrar: Managing Registrations

1. Go to **Academic → Registrations**.
2. Search by student or filter by semester, program, or status.
3. Registration records can be reviewed and managed here.

---

## 9. Timetable

**Who can use this**: Registrar, Super Admin, Lecturers (view)

### Creating a Timetable Entry

1. Go to **Academic → Timetable**.
2. Click **New Entry**.
3. Select a course offering, day of week, start/end time, and room/venue.
4. Save.

### Viewing the Semester Timetable

1. Click **View Semester** to see a full weekly grid.
2. Use the print button to generate a printable PDF.

---

## 10. Attendance

**Who can use this**: Lecturers, Registrar, Super Admin

### Taking Attendance

1. Go to **Academic → Attendance**.
2. Click **Take Attendance**.
3. Select the course offering and date.
4. Mark each student as **Present**, **Absent**, or **Late**.
5. Click **Save Attendance**.

Alternatively, use **Attendance by Program** to mark attendance for all courses in a program at once.

### Viewing Reports

1. Click **Attendance Report**.
2. Filter by course offering, date range, or student.
3. The report shows attendance percentages and flags students below the minimum threshold.

### Student View

Students can see their own attendance from:
- The dashboard attendance widget.
- **Academic → Attendance → My Attendance**.

---

## 11. Examinations

**Who can use this**: Registrar, Lecturers, Super Admin

### Scheduling an Examination

1. Go to **Academic → Examinations**.
2. Click **New Examination**.
3. Select the course offering, exam date, time, venue, and duration.
4. Save.

### Generating a Seating Plan

1. Open an examination record.
2. Click **Seating Plan**.
3. The system generates an alphabetical seating arrangement based on enrolled students.
4. Print using your browser.

---

## 12. Grades & Results

**Who can use this**: Lecturers (enter), Registrar (approve), Super Admin, Students (view own)

### Entering Results

**Method 1 — From the Results Module:**

1. Go to **Academic → Results → Entry**.
2. Select the course offering.
3. For each student, enter the CA score (out of 40) and Exam score (out of 60).
4. The total, grade, and grade points calculate automatically.
5. Click **Save Results**.

**Method 2 — From the Student Profile:**

1. Open a student's profile.
2. Go to the **Results** tab.
3. Click **Add Result**, select the course offering, and enter scores.

### Approving Results

Results start with a **Pending** status.

1. Go to **Academic → Results**.
2. Filter by semester/course.
3. Click the checkmark (✓) next to a result to approve it.
4. Approved results are visible to students.

### GPA Calculation

1. Go to **Academic → Results → Calculate GPA**.
2. Select the student and semester.
3. Click **Calculate** — the system computes GPA and CGPA from all approved results.

### Student Viewing Results

Students see their results in:
- The dashboard (latest semester results).
- **My Results** (full history with per-semester GPA).
- **Transcript** (printable document).

---

## 13. Grade Appeals

**Who can use this**: Students (submit), Registrar, Super Admin (review)

### Submitting an Appeal (Student)

1. Go to **Grade Appeals** in the sidebar.
2. Click **New Appeal**.
3. Select the result you wish to appeal.
4. Provide a detailed reason (minimum 30 characters).
5. Optionally upload supporting documents.
6. Submit.

You can only appeal results that do not already have a pending or under-review appeal.

### Reviewing an Appeal (Registrar/Admin)

1. Go to **Grade Appeals** in the sidebar. The badge shows the number of pending appeals.
2. Click an appeal to open it.
3. Review the student's reason and supporting document.
4. Enter admin notes.
5. If approving, enter the revised grade and total.
6. Click **Approve** or **Reject**.

Approval automatically updates the student's result record and notifies the student.

---

## 14. Student Holds

**Who can use this**: Registrar, Finance Officer, Super Admin

Holds prevent students from registering for courses until the hold is resolved.

### Placing a Hold

**From the Holds module:**
1. Go to **Academic → Student Holds**.
2. Click **Place Hold**, search for the student.
3. Select hold type (Financial, Academic, Disciplinary, Library, Hostel, Administrative).
4. Enter a reason.
5. Tick **Block Course Registration** if the hold should prevent registration.
6. Save.

**From the Student Profile:**
1. Open the student's profile.
2. Click the **Holds** tab.
3. Fill in the hold form on the left and click **Place Hold**.

### Releasing a Hold

1. Find the hold (from the Holds module or student profile Holds tab).
2. Click **Release** next to the active hold.
3. The hold is deactivated and the student can register again.

---

## 15. Graduation

**Who can use this**: Students (apply), Registrar, Super Admin (manage)

### Applying for Graduation (Student)

1. Go to **Graduation** in the sidebar.
2. Click **Apply for Graduation**.
3. Confirm your details and submit the application.

### Managing Applications (Staff)

1. Go to **Academic → Graduation**.
2. View all applications grouped by status.
3. Click **Eligible Students** to see who has met the credit requirements.

### Processing an Application

1. Open an application.
2. Review clearance checklist (Finance, Library, Hostel, etc.).
3. Update each clearance item.
4. Click **Approve** to advance the student to graduation status.
5. The student status is automatically updated to **Graduated**.

### Graduation Ceremonies

1. Go to **Graduation → Ceremonies**.
2. Click **New Ceremony** — set date, venue, and theme.
3. Approved graduates are linked to the upcoming ceremony.

### Graduation Certificates

1. Open an approved graduation application.
2. Click **Preview Certificate** to see how it looks.
3. Click **Download Certificate** to generate the PDF.

---

## 16. E-Learning

**Who can use this**: Lecturers (create and manage), Students (access and take quizzes)

### Creating an E-Learning Course (Lecturer)

1. Go to **E-Learning** in the sidebar.
2. Click **New Course**.
3. Fill in the title, description, and link it to a course offering.
4. Save.
5. Click **Publish** to make it visible to students.

### Adding Lessons

1. Open your e-learning course.
2. Click **Add Lesson** — enter a title and order number.
3. Within the lesson, click **Add Item** to attach:
   - A text/HTML block
   - A PDF or document file
   - A video link

### Creating a Quiz

1. Open the course, go to the **Quizzes** section.
2. Click **Create Quiz** — enter title, time limit, and passing score.
3. Add questions (multiple choice) with correct answer indicators.
4. Save and assign to a lesson.

### Taking a Quiz (Student)

1. Open the course from **E-Learning**.
2. Navigate to the relevant lesson and click **Take Quiz**.
3. Answer each question and submit before the timer runs out.
4. View your score and correct answers on the results page.

---

## 17. Finance — Fee Structures

**Who can use this**: Finance Officer, Super Admin

A fee structure defines what a student must pay for a specific program and semester.

### Creating a Fee Structure

1. Go to **Finance → Fee Structures**.
2. Click **New Fee Structure**.
3. Select the academic year, semester, program, and student type.
4. Enter the total amount.
5. Save.

### Fee Items

After saving the structure, you can break it into line items (Tuition, Registration Fee, Library Levy, etc.) from the show page.

### Cloning a Fee Structure

To copy a previous year's structure:
1. Open the existing structure.
2. Click **Clone** and adjust the amounts.

---

## 18. Finance — Billing

**Who can use this**: Finance Officer, Registrar, Super Admin, Students (view own)

Bills are generated from fee structures and assigned to students.

### Generating Bills

1. Go to **Finance → Billing**.
2. Click **Generate Bills**.
3. Select the academic year, semester, and the group (all students, specific program, or student type).
4. The system creates a bill for each student using the applicable fee structure.

### Viewing a Bill

1. Click a student's name in the billing list, or search by student ID.
2. The bill shows itemised charges, amount paid, and outstanding balance.
3. Click **Invoice** to generate a printable PDF invoice.

### Student Bill Access

Students can see their bills from:
- The dashboard financial widget.
- **Finance → My Bill** in the sidebar.

---

## 19. Finance — Payments

**Who can use this**: Finance Officer, Super Admin, Students (view own)

### Recording a Payment

1. Go to **Finance → Payments**.
2. Click **New Payment**.
3. Search for the student and select their bill.
4. Enter payment details:
   - Amount paid
   - Payment method (Cash, Bank Transfer, Mobile Money, etc.)
   - Transaction reference / receipt number
   - Payment date
5. Click **Save**.

### Verifying a Payment

Payments start as **Pending**.

1. Open a payment record.
2. Click **Verify** to confirm it as received.

### Reversing a Payment

If a payment was recorded in error:
1. Open the payment record.
2. Click **Reverse** and confirm.
3. The bill balance is restored.

### Payment Receipts

1. Open a verified payment.
2. Click **Receipt** to generate a printable receipt PDF.

---

## 20. Finance — Scholarships

**Who can use this**: Finance Officer, Super Admin

### Creating a Scholarship

1. Go to **Finance → Scholarships**.
2. Click **New Scholarship**.
3. Enter name, type (full/partial/bursary), amount or percentage, and eligibility criteria.
4. Save.

### Awarding a Scholarship

1. Open a scholarship.
2. Click **Award Scholarship**.
3. Search for the student and select the academic year.
4. Save — the discount is applied to the student's bill automatically.

### Revoking an Award

1. Open the scholarship detail page.
2. Find the award in the recipients list.
3. Click **Revoke** to remove the discount.

---

## 21. Finance — Reports

**Who can use this**: Finance Officer, Registrar, Super Admin

1. Go to **Finance → Reports**.
2. Choose a report type:
   - **Revenue** — total income by period
   - **Collection** — payments received in a date range (exportable)
   - **Outstanding** — students with unpaid balances (exportable)
   - **Scholarships** — scholarship awards and total value

Use date range filters and program/year selectors to narrow results. Click **Export PDF** or **Export Excel** where available.

---

## 22. Department Budgets

**Who can use this**: Finance Officer, Registrar, Super Admin

### Creating a Budget

1. Go to **Finance → Dept. Budgets** in the sidebar.
2. Click **New Budget**.
3. Select a department, academic year, and enter the fiscal year label and total amount.
4. Save. Budget starts in **Draft** status.

### Approving a Budget

1. Open the budget.
2. Click **Approve** — status changes to Active.

### Recording Transactions

1. Open an active budget.
2. Use the **Record Transaction** panel on the left:
   - Select type (Expense, Allocation, Adjustment, Transfer).
   - Enter category, amount, date, and optional reference.
3. Click **Record**.

The summary cards at the top update automatically showing total spent, remaining balance, and usage percentage.

---

## 23. HR — Employees

**Who can use this**: HR Officer, Super Admin

### Adding an Employee

1. Go to **HR → Employees**.
2. Click **New Employee**.
3. Fill in:
   - Personal details (name, email, phone)
   - Department and designation
   - Employment type (Permanent / Contract / Part-time)
   - Join date and salary
   - National ID number
4. A login account is created automatically using the email provided.
5. Save.

### Editing an Employee

1. Open the employee record.
2. Click **Edit**.
3. The **Employee Details** tab lets you update name, email, phone, department, designation, salary, and status.
4. The **Documents** tab lets you upload certificates, CVs, NRC copies, etc.

### Employee Status

Change status from the edit form:
- **Active** — currently working
- **On Leave** — on approved leave
- **Inactive** — temporarily not working
- **Terminated** — employment ended

### Uploading Documents

1. On the employee edit page, click the **Documents** tab.
2. Click **Upload Document**.
3. Select document type (NRC, CV, Qualification, Accreditation), add a title, and choose the file.
4. Save.

---

## 24. HR — Leave Management

**Who can use this**: Employees (apply), HR Officer (approve), Super Admin

### Applying for Leave (Employee)

1. Go to **HR → Leave Requests**.
2. Click **Apply for Leave**.
3. Select leave type, start date, end date.
4. Enter reason and any notes.
5. Submit.

### Approving or Rejecting (HR Officer)

1. Go to **HR → Leave Requests**.
2. Click a pending request to open it.
3. Click **Approve** or **Reject**.
4. The employee is notified of the decision.

### Leave Types

HR Officers can manage leave categories:
1. Go to **HR → Leave Types**.
2. Add, edit, or remove leave types (Annual, Sick, Maternity, Paternity, etc.) with their maximum allowed days.

---

## 25. HR — Payroll

**Who can use this**: HR Officer, Super Admin

### Payroll Configuration

Before running payroll, configure settings:
1. Go to **HR → Payroll Config**.
2. Set PAYE tax rates, NAPSA contribution rate, and other deduction rules.
3. Add allowance types (Housing, Transport, Medical).
4. Add deduction types (Loan repayment, union dues).

### Generating Payroll

1. Go to **HR → Payroll**.
2. Click **Generate Payroll**.
3. Select the month/year and department (or all departments).
4. The system calculates gross pay, allowances, deductions, PAYE, NAPSA, and net pay.
5. Review the generated payroll records.

### Processing Payroll

1. Once reviewed, click **Process** to mark the payroll as paid.

### Payslips

1. Open a payroll record.
2. Click **Payslip** to view or print the individual payslip.
3. Employees can access their own payslips from the HR section.

### Payroll Report

1. Go to **HR → Payroll → Report**.
2. Select the period.
3. Export as PDF for record keeping.

---

## 26. HR — Salary Advances

**Who can use this**: Employees (request), HR Officer (approve), Super Admin

### Requesting an Advance (Employee)

1. Go to **HR → Salary Advances**.
2. Click **New Request**.
3. Enter the amount needed and reason.
4. Submit.

### Approving an Advance

1. Open the request.
2. Click **Approve** or **Reject**.
3. Once approved, click **Mark as Paid** when the amount is disbursed.

---

## 27. Library

**Who can use this**: Librarians, Super Admin, Students (view own borrowings)

### Managing Books

1. Go to **Library → Books**.
2. Click **New Book** — enter title, author, ISBN, publisher, category, and number of copies.
3. The show page displays borrowing history and current availability.

### Issuing a Book

1. Go to **Library → Borrowings**.
2. Click **Issue Book**.
3. Search for the student and the book.
4. Set the due date.
5. Click **Issue**.

### Returning a Book

1. Go to **Library → Borrowings**.
2. Find the active borrowing record.
3. Click **Return** — the system automatically calculates any fine for late return.

### Renewing a Borrowing

1. Open a borrowing record.
2. Click **Renew** to extend the due date (subject to no other holds or reservations).

### Managing Fines

1. Go to **Library → Fines**.
2. View outstanding fines.
3. Click **Collect Fine** when the student pays.
4. Click **Waive Fine** to cancel a fine (with reason).

### Overdue Books

1. Go to **Library → Overdue**.
2. View all overdue borrowings with the number of days overdue and fine accrued.
3. Use this list to send reminders or apply library holds.

---

## 28. Hostel Management

**Who can use this**: Hostel Manager, Super Admin

### Creating a Hostel

1. Go to **Hostel → Hostels**.
2. Click **New Hostel** — enter name, gender type (male/female/mixed), and capacity.

### Managing Rooms

1. Go to **Hostel → Rooms**.
2. Click **New Room** — select the hostel, enter room number, floor, room type, and capacity.

### Allocating a Room

1. Go to **Hostel → Allocations**.
2. Click **Assign Room**.
3. Search for a student.
4. Select an available room.
5. Set the allocation date and expected vacate date.
6. Save.

### Recording Checkout

1. Open an allocation record.
2. Click **Checkout** — enter the actual vacate date.
3. The room is freed for re-allocation.

### Occupancy Report

1. Go to **Hostel → Occupancy**.
2. View a room-by-room breakdown of occupancy with student names and dates.
3. Click **Export** to generate a PDF report.

---

## 29. Asset Management

**Who can use this**: Finance Officer, Super Admin

### Adding an Asset

1. Go to **Assets** in the sidebar.
2. Click **New Asset**.
3. Enter:
   - Asset name, code/tag, category
   - Purchase date and purchase price
   - Location and condition
   - Expected useful life (years)
4. Save.

### Assigning an Asset

1. Open an asset record.
2. Click **Assign** — select the employee responsible for the asset.

### Recording Depreciation

1. Open an asset record.
2. Click **Depreciate** — enter the depreciation date and amount.
3. The current book value updates automatically.

### Depreciation Report

Go to **Assets → Depreciation Report** for a summary of all assets, their depreciation history, and current net book value.

---

## 30. Research Management

**Who can use this**: Lecturers, Staff, Super Admin

### Creating a Research Project

1. Go to **Research** in the sidebar.
2. Click **New Project**.
3. Enter title, abstract, start and end dates, and funding amount.
4. Assign the principal investigator.
5. Save.

### Adding Publications

1. Open a research project.
2. Click **Add Publication** — enter the journal name, title, year, and DOI.

### Managing Research Papers

1. Go to **Research → Papers**.
2. Click **Upload Paper** — attach a PDF file with title, authors, and keywords.
3. Uploaded papers can be downloaded by authorised staff.

---

## 31. Announcements

**Who can use this**: Registrar, Staff, Super Admin (create), All users (view)

### Creating an Announcement

1. Go to **Announcements**.
2. Click **New Announcement**.
3. Enter title, content, target audience, and expiry date.
4. Tick **Send Email Notification** to email all targeted users.
5. Save.

### Publishing an Announcement

Announcements start as drafts. Click **Publish** to make them visible to users.

### Viewing Announcements

All users can view active announcements from the Announcements section. Recent announcements also appear on the dashboard.

---

## 32. Messaging

**Who can use this**: All authenticated users

### Composing a Message

1. Go to **Messages** in the sidebar.
2. Click **Compose**.
3. Search for the recipient by name or email.
4. Enter a subject and message body.
5. Click **Send**.

### Reading Messages

1. Go to **Messages → Inbox**.
2. Click a message to open the full conversation thread.

### Replying

1. Open a conversation.
2. Type your reply in the box at the bottom.
3. Click **Send Reply**.

### Sent Messages

View your sent messages under **Messages → Sent**.

---

## 33. Notifications

**Who can use this**: All authenticated users

### Viewing Notifications

Click the **bell icon** in the top navigation bar to see your 8 most recent notifications. Click any notification to mark it as read and navigate to the related item.

Click **View all notifications** to open the full notifications page.

### Marking as Read

- Click a notification in the dropdown to mark it as read.
- On the notifications page, click **Mark read** next to any unread item.
- Click **Mark All Read** to clear all unread notifications at once.

### Notification Preferences

1. Go to **Notifications → Preferences**.
2. Toggle **Email** and **SMS** on or off for each notification type:
   - Results published
   - Announcements
   - Leave approved/rejected
   - Payment received
   - General alerts
   - Messages
   - Billing
3. Click **Save Preferences**.

> Note: SMS notifications require valid phone numbers and are subject to system SMS configuration.

---

## 34. Documents

**Who can use this**: All authenticated users

### Uploading a Document

1. Go to **Documents** in the sidebar.
2. Click **Upload Document**.
3. Enter a title, category, and description.
4. Select the file and visibility (Private / Shared).
5. Click **Upload**.

### Downloading a Document

1. Browse the document list.
2. Click the document title.
3. Click **Download** to save the file.

---

## 35. Support Tickets

**Who can use this**: All authenticated users (create), Staff/Super Admin (manage)

### Submitting a Ticket

1. Go to **Support** in the sidebar.
2. Click **New Ticket**.
3. Enter a subject, describe the issue, and set the priority (Low / Medium / High / Urgent).
4. Submit.

### Tracking Your Ticket

1. Go to **Support → My Tickets**.
2. Click a ticket to see its status and any responses from staff.

### Responding to a Ticket (Staff)

1. Open a ticket.
2. Type a response in the reply box.
3. Click **Send Response**.

### Closing a Ticket

Once the issue is resolved, click **Close Ticket**. Users can reopen a closed ticket if the issue persists.

---

## 36. Alumni Management

**Who can use this**: Registrar, Super Admin

### Registering an Alumnus

1. Go to **Alumni** in the sidebar.
2. Click **New Alumni**.
3. Link to an existing student record or fill in details manually.
4. Enter graduation year, current employment, and contact details.
5. Save.

### Viewing Alumni

Browse the alumni list with filters for graduation year, program, and employment status.

---

## 37. Reports

**Who can use this**: Registrar, Finance Officer, Super Admin

Go to **Reports** in the sidebar for system-wide reporting.

| Report | Description |
|--------|-------------|
| Students | Student statistics by program, status, gender, year of study |
| Academic | Course registrations, results distribution, GPA averages |
| Finance | Revenue, outstanding balances, payment methods breakdown |
| Attendance | Attendance rates by course, program, and semester |
| Admissions | Application counts, approval rates, program preferences |
| Hostel | Occupancy rates, room type breakdown |
| Scholarships | Total awarded, recipients by program |
| Login Activity | User login history and last active timestamps |

Each report supports filters (date range, program, department, academic year) and can be exported to **PDF** or **Excel**.

---

## 38. Admin Settings

**Who can use this**: Super Admin only

### System Settings

Go to **Settings** in the sidebar to configure:

- **University Information**: Name, short name, address, phone, email, website
- **Branding**: Upload logo and favicon
- **Hero Images**: Manage landing page images
- **Currency**: Symbol and code (e.g., K / ZMW)
- **Library Settings**: Daily fine rate
- **Registration**: Open/close course registration system-wide
- **Course Types**: Manage available course type labels

### User Management

1. Go to **Admin → Users**.
2. View, create, edit, or deactivate user accounts.
3. Assign roles to users from the edit screen.
4. Use **Toggle Status** to activate or deactivate an account without deleting it.

### Role Management

1. Go to **Admin → Roles**.
2. Create custom roles with specific permissions.
3. Assign permissions from the available permission list.

### Grade Scales

1. Go to **Admin → Academic Settings**.
2. Manage the grading scale (grade letter, minimum score, grade points).
3. Reorder or delete grade entries.

### Email Notifications (Bulk)

1. Go to **Admin → Email Notifications**.
2. Compose a message.
3. Select the target group (all users, students only, staff only, specific department or role).
4. Click **Send**.

### SMS Notifications (Bulk)

1. Go to **Admin → SMS Notifications**.
2. Compose an SMS (max 160 characters).
3. Select target group.
4. Click **Send**.

> SMS requires Africa's Talking credentials configured in `.env` (SMS_DRIVER, SMS_USERNAME, SMS_API_KEY).

### Audit Logs

Go to **Admin → Audit Logs** to view a complete history of who did what in the system — with timestamps, IP addresses, and changed data.

### Backups

1. Go to **Admin → Backup**.
2. Click **Create Backup** to generate a database backup.
3. Click **Download** next to any backup file to save it locally.

> Keep backups in a secure location. Run backups before any major system changes.

---

## 39. Role Reference

| Role | Key Permissions |
|------|----------------|
| **super-admin** | Full access to all modules and settings |
| **registrar** | Students, admissions, academic structure, courses, results, graduation, holds, reports |
| **finance** | Fee structures, billing, payments, scholarships, finance reports, budgets |
| **hr** | Employees, leave, payroll, salary advances, appointments |
| **lecturer** | Course offerings (assigned), attendance, examinations, results entry, e-learning |
| **librarian** | Books, borrowings, fines, overdue management |
| **hostel-manager** | Hostels, rooms, allocations, occupancy reports |
| **student** | My courses, my results, my bill, my attendance, grade appeals, e-learning, graduation apply |
| **staff** | Announcements (view), messages, documents, support tickets, research |

---

*This manual covers all modules available in the system as of the June 2026 release. For technical support, submit a ticket via the Support module or contact the system administrator.*
