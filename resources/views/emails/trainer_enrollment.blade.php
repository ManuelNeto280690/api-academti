<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Novo Pedido de Inscrição - CEFTIC</title>
</head>
<body style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
        <h2 style="color: #00c8ff;">Olá, {{ $trainer->name }}!</h2>
        <p>Recebeste um novo pedido de inscrição para os teus cursos na plataforma <strong>CEFTIC</strong>.</p>
        
        <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Aluno:</strong> {{ $student->name }} ({{ $student->email }})</p>
        </div>

        <h3 style="color: #666; margin-bottom: 10px;">Cursos Solicitados:</h3>
        <ul style="list-style: none; padding: 0;">
            @foreach($courses as $course)
                <li style="padding: 10px; border-bottom: 1px solid #eee; display: flex; align-items: center;">
                    <div style="width: 8px; height: 8px; background: #00c8ff; border-radius: 50%; margin-right: 12px;"></div>
                    <span>{{ $course->title }}</span>
                </li>
            @endforeach
        </ul>

        <p style="margin-top: 25px;">A inscrição está pendente até que o aluno envie o comprovativo de transferência e seja confirmado pelo administrador.</p>
        
        <div style="margin: 30px 0; text-align: center;">
            <a href="{{ config('app.frontend_url') }}/formador/alunos" style="background: #00c8ff; color: #fff; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block;">
                Ver Gestão de Alunos
            </a>
        </div>

        <p style="font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px;">
            Este é um e-mail automático enviado pela plataforma CEFTIC. Por favor, não responda a este e-mail.
        </p>
    </div>
</body>
</html>
