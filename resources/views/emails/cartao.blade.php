<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="pt">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cartão de Identificação de {{ $asset->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f9fc; margin: 0; padding: 0;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                    <tr>
                        <td align="center" style="padding: 10px 0; border-bottom: 1px solid #ddd;">
                            <img src="{{ $message->embed(public_path('img/parquesegurobanneremail.png')) }}" alt="Banner" width="80%" style="max-width: 560px; height: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px;">
                            <h1 style="color: #1d3557; margin: 0 0 20px 0; font-size: 24px; text-align: center;">Cartão de Identificação</h1>
                            
                            <p style="color: #555555; font-size: 16px; margin: 0; padding-bottom: 10px; line-height: 1.6;">Exmo(a). Encarregado(a) de Educação,</p>
                            
                            <p style="color: #555555; font-size: 16px; margin: 0; padding-bottom: 10px; line-height: 1.6;">Segue em anexo o cartão de identificação do educando <strong>{{ $asset->name }}</strong>.</p>
                            
                            <div style="background-color: #e0f2f1; color: #00796b; border: 1px solid #b2dfdb; padding: 15px; border-radius: 5px; margin: 20px 0; font-size: 15px; line-height: 1.5;">
                                <p style="color: #00796b; margin: 0;">
                                    Para garantir um <strong>scan rápido e eficiente</strong>, por favor, imprima o cartão anexo em papel. Recomendamos também que o plastifique, se possível, para maior durabilidade.
                                </p>
                            </div>

                            <p style="color: #555555; font-size: 16px; margin: 0; padding-top: 20px; line-height: 1.6;">Com os melhores cumprimentos,<br/>
                            Junta de Freguesia do Parque das Nações</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="text-align: center; font-size: 12px; color: #666666; padding: 15px 0 0 0; border-top: 1px solid #ddd;">
                            <p style="margin: 0; padding-bottom: 5px;">Este é um e-mail gerado automaticamente. Por favor, não responda.</p>
                            <p style="margin: 0;">
                                Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações |
                                <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" rel="noopener noreferrer" style="color: #666666; text-decoration: none;">Política de Privacidade</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>