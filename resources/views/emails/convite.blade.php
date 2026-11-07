<!doctype html>
<html lang="pt-BR">
<body style="margin:0;padding:0;background:#012468;font-family:Montserrat,Arial,Helvetica,sans-serif">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#012468;padding:28px 12px">
  <tr><td align="center">
    <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%">
      <tr><td align="center" style="padding:0 0 18px">
        <div style="font-size:11px;font-weight:800;letter-spacing:4px;color:#F5A623;text-transform:uppercase">9º Festival de Gastronomia e Cultura</div>
        <div style="font-size:30px;font-weight:800;color:#ffffff;margin-top:4px">Aracati 2026</div>
        <div style="font-size:13px;color:#c9d6f5;margin-top:4px">13, 14 e 15 de novembro</div>
      </td></tr>
      <tr><td style="background:#ffffff;border-radius:16px;padding:28px;color:#012468">
        <p style="margin:0 0 12px;font-size:18px;font-weight:800">Olá, {{ $nome }}!</p>
        <p style="margin:0 0 14px;font-size:15px;line-height:1.55">
          Você recebeu acesso para preencher as informações do seu stand no <b>app do festival</b>:
          descrição, horário, Instagram, WhatsApp, cardápio e fotos. O que você salvar aparece no app na hora.
        </p>
        @if(count($stands))
        <p style="margin:0 0 6px;font-size:13px;color:#5a6b94">Stand(s) liberado(s) para você:</p>
        <ul style="margin:0 0 16px;padding-left:18px;font-size:15px;font-weight:700">
          @foreach($stands as $s)<li>{{ $s }}</li>@endforeach
        </ul>
        @endif
        <table role="presentation" cellpadding="0" cellspacing="0" style="margin:22px 0"><tr><td style="background:#F5A623;border-radius:10px">
          <a href="{{ $link }}" style="display:inline-block;padding:14px 26px;font-size:16px;font-weight:800;color:#012468;text-decoration:none">Criar minha senha</a>
        </td></tr></table>
        <p style="margin:0 0 6px;font-size:13px;color:#5a6b94">Se o botão não abrir, copie e cole este endereço no navegador:</p>
        <p style="margin:0 0 16px;font-size:12px;word-break:break-all;color:#0a47c9">{{ $link }}</p>
        <p style="margin:0;font-size:13px;color:#5a6b94;line-height:1.5">
          Este link vale por {{ $dias }} dias e é de uso pessoal. Depois de criar a senha, você entra sempre pelo mesmo endereço do site, em <b>Entrar</b>.
          Se você não esperava esta mensagem, pode ignorá-la.
        </p>
      </td></tr>
      <tr><td align="center" style="padding:16px 0 0;font-size:12px;color:#9fb2e6">Prefeitura Municipal de Aracati</td></tr>
    </table>
  </td></tr>
</table>
</body>
</html>
