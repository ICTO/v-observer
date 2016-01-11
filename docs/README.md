# Table of contents

[Authentication](#authentication)

[Dashboard](#dashboard)

[Groups](#groups)

[Questionnaire](#questionnaires)

[Videos](#videos)

[Analysis](#analysis)

[Export](#export)


# Authentication

## Login

There are 2 ways to receive a user account for the application.

- The application administrator creates a new user account for you.
- An already registered user can add a new user to his group.

## CAS authentication

To be able to login with the CAS authentication, the CAS username must be added to the user account.
This can be accomplished after logging in with your email and password and then edit your profile. A textfield is provided to add a CAS username to the account. If you don't have an email and password, you need to contact the application administrator to modify the user account.

## Forgot password

To reset your password, you need to have your registered email address. If you don't know your registered email address. Please contact the application administrator. After filling in the forgot password form, you will receive an email with a link to reset you password.

# Dashboard

After logging in, you will be viewing your dashboard. The dashboard gives you an overview of all the content that you own. Content that you own is only visible to yourself. If you need to share content, you will need to create a group.

# Groups

When you press the "Group" link in the left side navigation, you will view a list of all groups you are member of. You can create new groups by pressing the "new group" button. When pressing on one of the groups in your list, you will be redirected to the dashboard of the group. This dashboard displays all attached content and users. The list of attached users to this groups also has a label of the users role. You can change the role of a user by pressing the dots button next to the user. If no dots button is visible, you don't have access to change user roles or the group only has 1 member. Only group admins have the permission to change user roles. If you created a new group, you will be assigned as group admin. To add users to the group, press the "add user" button on the groups dashboard. If the user has already a user account of the application, you can select him and add him to the group. If the user has no account of the application, you can create a new account for him by pressing the button "create new user". You can check the box to send an email to the new user about his create account. He then will receive an email to set his password. If you provide a CAS username when creating the user, he will be able to login with CAS.

# Questionnaires

To create a new questionnaire, go to your dashboard or the dashboard of the chosen group and press the button "Add questionnaire". Fill in the title field and choose an owner. If the owner is a group, all members will be able to view the questionnaire. Only group admins will be able to adjust the questionnaire. After saving the questionnaire, you can start creating the questions. Press the add button and select "subtitle". A subtitle can be used to group multiple questions. You always need to start by creating a subtitle before you can add questions. Fill in the form and save the subtitle. After saving the subtitle, you can add a question by pressing the dots button next to the subtitle and choose the question type you want to add. Fill in the form and save your question. You can repeat this process until you have created the questionnaire you want. Questionnaires are locked and can't be modified anymore when the video analysis process is started.

You can reorder questions by dragging and dropping them where you want them. If the order is correct, press the "save order" button on the bottom of the page.

To change the interval of a questionnaire, select the questionnaire on the dashboard you want to edit and press the button "interval". The default interval of a questionnaire is 300 seconds. The interval is locked and can't be modified anymore when the video analysis process is started.

You can export and import questionnaires by pressing the buttons "Import" and "Export". You can use this feature to copy the questionnaire to a different group or environment.

# Videos

To add videos to a questionnaire, go to your dashboard or the dashboard of the chosen group and press the questionnaire you want to add a video to. Now press on the button "add video" and choose the type of video upload you like to use. If you don't see an "add video" button, you won't have access to upload a video to that group. You need to be a group admin to be able to add videos to a questionnaire. After pressing the button, give your video a name and press "add video". You can now upload the video file. A progressbar will indicate the uploading and transcoding progress of the video. After the upload is finished, you can navigate away from the page even if the transcoding job isn't finished. The transcoding job will continue in the background. After all jobs are finished, a video player starts playing the video.

# Analysis

To start the analysis of a video, open the video on the questionnaire page and press the button "analysis". All members of a group have access to start the analysis of a video. The analysis page opens with on the left side the video player and on the right side the questionnaire. The video player has a timeline that is divided into sections based on the interval set on the questionnaire. Each section of the timeline has a color based on the completeness of the attached questionnaire. The video player loops the section until the questionnaire is filled in completely and then switches to the next section. To fast navigate the sections of the timeline, you can use the arrows of your keyboard. After finishing all sections, you will be redirected to the overview page.

# Export

To export the analysis of a video, open the video on the questionnaire page and press the button "export analysis". You can now choose the type of export you like to use. Press the download button to start downloading the requested export file.
