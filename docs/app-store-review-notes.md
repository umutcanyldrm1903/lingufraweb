# App Store Review Notes Template

Use this text in `App Store Connect > App Review Information > Notes`.

Replace the placeholders before submission:

- `[STUDENT_REVIEW_EMAIL]`
- `[STUDENT_REVIEW_PASSWORD]`
- `[INSTRUCTOR_REVIEW_EMAIL]`
- `[INSTRUCTOR_REVIEW_PASSWORD]`

```text
Thank you. Below is the information for App Review.

1. Screen recording
We will attach a screen recording captured on a physical iPhone device. The recording starts from app launch and shows the typical core flow for both user roles.

The recording includes:
- app launch
- account registration
- login
- student flow
- instructor flow
- account deletion
- package/payment screen
- joining a live lesson
- permission prompts that may appear during review (camera, microphone, photo library)

2. App purpose
LinguFranca is a mobile app for an online language learning platform. It helps students book and attend live language lessons with instructors, purchase lesson packages, access homework, library items, reports, and messaging, and track their learning progress. It also helps instructors manage availability, lessons, homework, library content, reports, and student communication.

The app solves the problem of managing online language education in one place instead of splitting the experience across separate tools for booking, payments, lesson access, messaging, and progress tracking.

3. Review instructions and test credentials
Please review the app using the following demo accounts.

Student demo account
Email: [STUDENT_REVIEW_EMAIL]
Password: [STUDENT_REVIEW_PASSWORD]

Instructor demo account
Email: [INSTRUCTOR_REVIEW_EMAIL]
Password: [INSTRUCTOR_REVIEW_PASSWORD]

Important review notes:
- The student demo account should already have active lesson/package credit.
- The student demo account should have at least one upcoming live lesson.
- The instructor demo account should have at least one scheduled lesson.

Suggested review flow:
Student:
- Launch app
- Register a new student account or use the student demo account
- Log in
- Open student dashboard
- Open packages/payment
- Open instructors and reserve a lesson
- Open lessons and join a live lesson
- Open homework/library/reports
- Open messages
- Open profile/settings and show account deletion

Instructor:
- Log in with the instructor demo account
- Open instructor dashboard
- Review lessons, students, library, homework, reports, and messages
- Start/join a live lesson
- Open profile/settings and show account deletion

Account deletion paths:
- Student: Profile/Settings > Delete Account
- Instructor: Profile > Delete Account

Messaging safety tools:
- Both student and instructor chat screens include user reporting and block/unblock controls.

4. External services, tools, and platforms used by the app
The app uses the following external services for core functionality:
- Custom Laravel backend API for authentication, scheduling, messaging, homework, reports, profile management, and content delivery
- Zoom Meeting SDK / Zoom platform for native in-app live lessons
- Iyzico for payment processing of lesson packages
- Backend email delivery for account verification and password reset

5. Regional differences
The app’s core functionality is consistent across regions. Authentication, lesson management, messaging, reports, homework, and live lesson participation work the same way in all regions.

Payment availability may vary depending on payment processor and merchant configuration. For review, please use the provided demo student account with active package access so a real purchase is not required.

6. Regulated industry
This app is not in a highly regulated industry. It is an online language education service platform.
```

## Physical Device Recording Checklist

Record on a real iPhone:

1. Launch app from home screen
2. Show registration screen
3. Show login flow
4. Student:
   - dashboard
   - package/payment screen
   - instructor reservation
   - live lesson join
   - homework/library/reports
   - messages
   - account deletion
5. Instructor:
   - dashboard
   - lessons
   - messages
   - homework/library/reports
   - live lesson join/start
   - account deletion
6. Include permission prompts if they appear
