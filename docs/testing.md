Testing for SkillSwap



Overview



We test SkillSwap to make sure our features work properly and to find bugs early. For now, we mainly test manually using XAMPP, phpMyAdmin and our browser.




Test Environment:

We use XAMPP to run our website locally, phpMyAdmin to check the database, VS Code for coding and GitHub to manage our changes. We also use Azure DevOps to keep track of tasks and bugs.



Sprint 2 Tests



* Registration
* Register a new user
* Try an email that already exists
* Test invalid emails and short passwords
* Check if passwords are stored as hashes
* Check if privacy consent is required
* Login and Logout
* Log in with correct credentials
* Try logging in with a wrong password
* Log out and check if the session ends



User Profile



* Create a user profile
* Edit existing profile information
* Check if changes are saved in the database
* Make sure users cannot edit other profiles



Skills



* Check if all 30 predefined skills are available



* Add offered and wanted skills



* Try adding the same skill twice



* Check if only approved skills can be selected



Test Results:

So far, we have successfully tested the database connection, user registration and password hashing. Creating user profiles also works, and we can load the skill catalogue and add both offered and wanted skills.



Login and logout have been partially tested. We still need to check profile editing more thoroughly and make sure users cannot add the same skill twice.



Next Steps:

We still need to test some features more carefully, especially profile editing, error messages and user permissions.

In Sprint 3, we will focus on testing the matching system, Like/Pass decisions and communication between users.

We also want to add some basic automated tests later, so we don't have to test everything manually after every change.


Conclusion:

Our basic backend features are working locally. We will continue testing new features, fixing bugs and improving our tests as we develop SkillSwap.

Sprint 3 Tests

Accessibility Testing (Task #77)

Date: 25 November 2026

Tool: Google Chrome Lighthouse (Desktop)

Pages tested:
- Discover: 100/100
- My Profile: 100/100
- My Matches: 100/100

Manual tests:
- Keyboard navigation with Tab and Shift + Tab: Passed
- Visible keyboard focus: Passed
- Profile form and skill selection with keyboard: Passed
- Browser zoom at 200%: Passed
- No overlapping or cut-off elements observed

Result:
All three tested pages passed our basic accessibility
checks. Lighthouse reported no automatic accessibility
issues.

(Note:
These tests do not guarantee full WCAG compliance.
The chat screens still need to be tested after integration.
)