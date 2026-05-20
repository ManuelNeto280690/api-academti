@extends('emails.layout')

@section('content')
    <h2>Olá, {{ $user->name }}!</h2>
    <p>É com grande prazer que lhe damos as boas-vindas à **Ceftic Angola**. A sua conta foi criada com sucesso e já pode começar a explorar a nossa plataforma de aprendizagem.</p>

    @if($password)
        <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; padding: 20px; border-radius: 8px; margin: 25px 0;">
            <p style="margin: 0; font-size: 14px; color: #64748b;">As suas credenciais de acesso temporárias são:</p>
            <p style="margin: 5px 0 0 0; font-family: monospace; font-size: 18px; color: #0f172a;">
                <strong>Email:</strong> {{ $user->email }}<br>
                <strong>Senha:</strong> {{ $password }}
            </p>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #ef4444;">* Recomendamos que altere a sua senha após o primeiro acesso.</p>
        </div>
    @else
        <p>Aceda à plataforma com o email e senha que definiu durante o registo.</p>
    @endif

    <div style="text-align: center;">
        <a href="http://localhost:3000/login" class="button">Aceder à Minha Conta</a>
    </div>

    <p>Se tiver alguma dúvida, a nossa equipa de suporte está sempre disponível para ajudar. Basta responder a este email.</p>
    
    <p>Bons estudos!<br>
    <strong>Equipa Ceftic</strong></p>
@endsection
