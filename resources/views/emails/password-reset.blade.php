<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="x-apple-disable-message-reformatting" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Plus+Jakarta+Sans:wght@700&display=swap"
        rel="stylesheet">
    <title><b>Reset Your EHR Password</b></title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #fcfcfc;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
        }

        table {
            border-collapse: collapse;
        }

        .heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.04em;
            color: #035022;
            margin: 0 0 24px 0;
        }

        .body-text {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #374151;
            margin-bottom: 30px;
        }

        .btn {
            background-color: #035022;
            color: #ffffff !important;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 15px;
            display: inline-block;
            border: 1px solid #035022;
        }

        .footer {
            background-color: #f3f4f6;
            border-top: 1px solid #e5e7eb;
            padding: 48px 24px;
            text-align: left;
        }

        .footer-link {
            color: #035022;
            text-decoration: underline;
        }

        .footer-legal {
            font-family: 'Inter', sans-serif;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.8;
        }

        .content-container {
            max-width: 600px;
            width: 100%;
            background-color: #ffffff;
            margin: 40px auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .inner-padding {
            padding: 48px;
        }

        @media only screen and (max-width: 600px) {
            .inner-padding {
                padding: 32px 20px;
            }

            .heading {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <div class="content-container">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                        <tr>
                            <td style="padding: 40px 48px 0;">
                                <table border="0" cellspacing="0" cellpadding="0" role="presentation">
                                    <tr>
                                        <td style="vertical-align: middle; padding-right: 16px;">
                                            <img src="https://electronichealthrecord.bscs3a.com/img/ehr-logo.png"
                                                alt="EHR Logo" width="50"
                                                style="display: block; margin-bottom: 16px; border: 0;" />
                                        </td>
                                        <td
                                            style="vertical-align: middle; border-left: 1px solid #e5e7eb; padding-left: 16px;">
                                            <div
                                                style="width: 32px; height: 3px; background-color: #EDB62C; margin-bottom: 6px;">
                                            </div>
                                            <p
                                                style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 11px; font-weight: 700; letter-spacing: 0.12em; color: #035022; text-transform: uppercase; margin: 0;">
                                                Electronic Health Record
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="inner-padding">
                        <h1 class="heading">Security Verification</h1>
                        <p class="body-text">
                            Hello {{ $user->full_name }},<br /><br />
                            A password reset was requested for your EHR account. To maintain the security of your
                            clinical data, please use the 6-digit code below to establish a new password. This secure code is
                            active for <strong>60 minutes</strong>.
                        </p>
                        <div style="background-color: #f3f4f6; padding: 24px; border-radius: 8px; text-align: center; margin-bottom: 30px;">
                            <span style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 32px; font-weight: 700; letter-spacing: 0.2em; color: #035022;">
                                {{ $code }}
                            </span>
                        </div>
                        <p class="body-text" style="margin-top: 40px; font-size: 14px; color: #6b7280;">If you did not
                            initiate this request, no action is required. Your account remains secure and your current
                            password will not be changed.</p>
                    </div>

                    <div class="footer">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" role="presentation">
                            <tr>
                                <td class="footer-legal">
                                    <p style="margin: 0 0 16px 0; font-weight: 600; color: #374151;">
                                        <img src="https://img.icons8.com/ios-filled/24/035022/shield.png" width="18"
                                            style="vertical-align: middle; margin-right: 6px;" alt="" />
                                        Electronic Health Record Security Team
                                    </p>
                                    <p style="margin: 0 0 16px 0;">This is an automated security notification. Please do
                                        not reply to this email. For technical assistance, contact your system
                                        administrator or visit our <a href="#" class="footer-link">Help Center</a>.</p>
                                    <p style="margin: 0;">&copy; 2026 EHR System. All rights
                                        reserved.<br />Confidentiality Notice: This system is for authorized clinical
                                        use only.</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </td>
        </tr>
    </table>
</body>

</html>