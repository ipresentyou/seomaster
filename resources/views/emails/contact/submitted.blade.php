<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neue Kontaktanfrage</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e8f0fe;
        }
        .logo {
            margin-bottom: 16px;
        }
        .logo img {
            width: 180px;
            height: auto;
        }
        .header h1 {
            color: #1a73e8;
            font-size: 24px;
            margin: 0;
            font-weight: 600;
        }
        .header p {
            color: #5f6368;
            margin: 8px 0 0 0;
            font-size: 14px;
        }
        .content {
            margin-bottom: 32px;
        }
        .field-group {
            margin-bottom: 20px;
        }
        .field-label {
            font-weight: 600;
            color: #202124;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .field-value {
            color: #5f6368;
            font-size: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #1a73e8;
        }
        .message-field {
            white-space: pre-wrap;
            line-height: 1.5;
        }
        .footer {
            margin-top: 32px;
            padding-top: 20px;
            border-top: 1px solid #dadce0;
            text-align: center;
            color: #80868b;
            font-size: 12px;
        }
        .footer a {
            color: #1a73e8;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #e8f0fe;
            color: #1a73e8;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/logo_seomaster.svg') }}" alt="SEOmaster Logo">
            </div>
            <h1>📧 Neue Kontaktanfrage</h1>
            <p>SEOmaster Support System</p>
        </div>

        <div class="content">
            <div class="field-group">
                <div class="field-label">
                    👤 Absender
                    <span class="badge">{{ $data['name'] }}</span>
                </div>
                <div class="field-value">{{ $data['email'] }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">📋 Betreff</div>
                <div class="field-value">{{ $data['subject'] }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">💬 Nachricht</div>
                <div class="field-value message-field">{{ $data['message'] }}</div>
            </div>

            <div class="field-group">
                <div class="field-label">🕐 Eingegangen am</div>
                <div class="field-value">{{ now()->format('d.m.Y H:i') }} Uhr</div>
            </div>
        </div>

        <div class="footer">
            <p>
                <strong>SEOmaster SaaS Platform</strong><br>
                <a href="https://seomaster.ddev.site/admin">Zum Admin-Panel</a>
            </p>
        </div>
    </div>
</body>
</html>
