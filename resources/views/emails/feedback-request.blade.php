<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Новая заявка</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111111;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:920px;background:#ffffff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
                    <tr>
                        <td style="padding:18px 24px 14px 24px;text-align:center;border-bottom:1px solid #e5e7eb;">
                            <div style="font-size:20px;line-height:1.2;font-weight:700;color:#111111;">Atlantic Group</div>
                            <div style="margin-top:6px;font-size:13px;line-height:1.4;color:#4b5563;">Новая заявка с сайта</div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:22px 24px 24px 24px;">
                            <div style="font-size:12px;color:#6b7280;letter-spacing:.04em;text-transform:uppercase;margin:0 0 8px 0;">Сообщение</div>
                            <div style="padding:14px 16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;font-size:16px;line-height:1.65;color:#111111;white-space:pre-wrap;">{{ $message !== '' ? $message : '—' }}</div>

                            <div style="height:1px;background:#e5e7eb;margin:20px 0 16px 0;"></div>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
                                <tr>
                                    <td style="padding:0 12px 0 0;vertical-align:top;white-space:nowrap;">
                                        <div style="font-size:11px;color:#6b7280;letter-spacing:.04em;text-transform:uppercase;">Имя</div>
                                        <div style="margin-top:4px;font-size:15px;line-height:1.35;font-weight:600;color:#111111;">{{ $name !== '' ? $name : '—' }}</div>
                                    </td>
                                    <td style="padding:0 12px 0 12px;vertical-align:top;white-space:nowrap;">
                                        <div style="font-size:11px;color:#6b7280;letter-spacing:.04em;text-transform:uppercase;">Телефон</div>
                                        <div style="margin-top:4px;font-size:15px;line-height:1.35;font-weight:600;color:#111111;">{{ $phone !== '' ? $phone : '—' }}</div>
                                    </td>
                                    <td style="padding:0 0 0 12px;vertical-align:top;">
                                        <div style="font-size:11px;color:#6b7280;letter-spacing:.04em;text-transform:uppercase;">Email</div>
                                        <div style="margin-top:4px;font-size:15px;line-height:1.35;font-weight:600;color:#111111;word-break:break-all;">{{ $email !== '' ? $email : '—' }}</div>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-top:18px;font-size:12px;line-height:1.45;color:#6b7280;">
                                Страница отправки: {{ $source !== '' ? $source : '—' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
