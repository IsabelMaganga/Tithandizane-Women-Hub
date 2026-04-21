<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f7; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                    {{-- logo --}}
                    <tr>
                        <td style="padding: 20px; text-align: center; background-color: #f9fafb;">
                            <img src="https://via.placeholder.com/150x50?text=Your+Logo" alt="YourApp Logo" style="max-width: 150px; height: auto;">
                        </td>
                    </tr>

                    {{-- header --}}
                    <tr>
                        <td style="padding: 30px; text-align: center; background-color: #4f46e5; color: #ffffff;">
                            <h1 style="margin: 0; font-size: 24px;">Password Reset</h1>
                        </td>
                    </tr>

                    {{-- body --}}
                    <tr>
                        <td style="padding: 30px; color: #333333;">
                            <h2 style="font-size: 20px; margin-top: 0;">Hello,</h2>
                            <p style="font-size: 16px; line-height: 1.5; margin: 20px 0;">
                                You requested to reset your password. Click the button below to set a new one:
                            </p>
                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{{ url('/mentor/reset-password/' . $token) }}"
                                   style="background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-size: 16px; display: inline-block;">
                                    Reset Password
                                </a>
                            </p>
                            <p style="font-size: 14px; line-height: 1.5; color: #666666;">
                                If you did not request this, please ignore this email. For your security, this link will expire in <strong>2 minutes</strong>.
                            </p>
                        </td>
                    </tr>

                    {{-- fotter --}}
                    <tr>
                        <td style="padding: 20px; text-align: center; background-color: #f9fafb; font-size: 12px; color: #999999;">
                            &copy; {{ date('Y') }} Tithandizane-women-hub. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
