# SecSign ID
This app makes it possible to use the SecSign two-factor authentication SecSign ID
to authenticate users in Nextcloud.

Once installed, go to **Settings/Security** and add an existing SecSign ID to enable 2FA
for yourself. Make sure to also generate backup codes, so you always have a backup plan.

Admins can go to the SecSign ID tab and manage the SecSign IDs for every user and enable/disable
two-factor authentication.

With the new User Onboarding feature, enabling SecSign 2FA for all users is a very easy process. Simply enable User Onboarding and then choose a suffix for your users SecSign IDs. Make sure to either enforce 2FA for the user groups which should use SecSign 2FA or enable it for single users. Only users with Two-Factor Authentication enabled in the NextCloud settings can use SecSign 2FA.

If you do not already have a SecSign ID, go to [secsign.com](https://www.secsign.com/try-secsign-id-now/) to learn
how to create and use one.

[PHP QR Code library](http://phpqrcode.sourceforge.net/) was used for the creating QR codes. Many thanks to the developers.