<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; padding: 32px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; width: 100%;">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color: {{ $headerColor }}; padding: 24px 32px; border-radius: 12px 12px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 20px; font-weight: bold;">
                                {{ $siteName }} — Nouveau message
                            </h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 32px;">
                            {{-- Expediteur --}}
                            <h2 style="margin: 0 0 16px; font-size: 16px; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">
                                Expediteur
                            </h2>
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280; width: 120px;">Nom</td>
                                    <td style="padding: 6px 0; font-size: 14px; color: #1f2937; font-weight: 600;">{{ $contactData['nom'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Email</td>
                                    <td style="padding: 6px 0; font-size: 14px; color: #1f2937;">
                                        <a href="mailto:{{ $contactData['email'] }}" style="color: {{ $headerColor }}; text-decoration: none;">{{ $contactData['email'] }}</a>
                                    </td>
                                </tr>
                                @if(!empty($contactData['telephone']))
                                <tr>
                                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Telephone</td>
                                    <td style="padding: 6px 0; font-size: 14px; color: #1f2937;">{{ $contactData['telephone'] }}</td>
                                </tr>
                                @endif
                                @if(!empty($contactData['company']))
                                <tr>
                                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Societe</td>
                                    <td style="padding: 6px 0; font-size: 14px; color: #1f2937;">{{ $contactData['company'] }}</td>
                                </tr>
                                @endif
                                @if(!empty($contactData['sujet_label']))
                                <tr>
                                    <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Sujet</td>
                                    <td style="padding: 6px 0; font-size: 14px; color: #1f2937;">{{ $contactData['sujet_label'] }}</td>
                                </tr>
                                @endif
                            </table>

                            {{-- Message --}}
                            <h2 style="margin: 0 0 12px; font-size: 16px; color: #1f2937; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px;">
                                Message
                            </h2>
                            <div style="background-color: #f9fafb; border-radius: 8px; padding: 16px; font-size: 14px; line-height: 1.6; color: #374151; white-space: pre-wrap;">{{ $contactData['message'] }}</div>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 16px 32px; border-radius: 0 0 12px 12px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; font-size: 12px; color: #9ca3af; text-align: center;">
                                Ce message a ete envoye via le formulaire de contact de {{ $siteName }}.
                                Vous pouvez repondre directement a cet email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
