<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Carteira Financeira')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="container">
        <div class="topbar">
            @auth
                <span class="topbar-user">Logado como: <strong>{{ Auth::user()->name }}</strong> <span class="topbar-email">({{ Auth::user()->email }})</span></span>
                <form method="POST" action="/logout" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-small">Sair</button>
                </form>
            @else
                <a href="/login" class="btn btn-secondary">Entrar</a>
                <a href="/register" class="btn btn-secondary">Cadastrar</a>
            @endauth
        </div>
    </div>

    <div class="container">
        <div class="header">
            <nav class="nav">
                <div>
                    <a href="/" class="brand">💰 Carteira Financeira</a>
                </div>
                <ul class="nav-links">
                    @auth
                        <li><a href="/dashboard">Painel</a></li>
                        <li><a href="/transactions">Transações</a></li>
                        <li><a href="/transactions/transfer">Transferir</a></li>
                        <li><a href="/transactions/deposit">Depositar</a></li>
                    @endauth
                </ul>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        // Máscara para campos monetários
        document.addEventListener('DOMContentLoaded', function() {
            const moneyInputs = document.querySelectorAll('input[name="amount"]');

            moneyInputs.forEach(input => {
                // Adicionar prefixo R$
                input.type = 'text';
                input.inputMode = 'decimal';

                // Formatar valor ao digitar
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');

                    if (value === '') {
                        e.target.value = '';
                        return;
                    }

                    // Converter para decimal
                    value = (parseInt(value) / 100).toFixed(2);

                    // Formatar para Real brasileiro
                    e.target.value = 'R$ ' + value.replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                });

                // Ao enviar o formulário, converter de volta para número
                input.closest('form').addEventListener('submit', function(e) {
                    moneyInputs.forEach(inp => {
                        const cleanValue = inp.value
                            .replace('R$', '')
                            .replace(/\s/g, '')
                            .replace(/\./g, '')
                            .replace(',', '.');

                        // Criar input hidden com valor numérico
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = inp.name;
                        hiddenInput.value = cleanValue;

                        // Remover name do input original para não enviar
                        inp.removeAttribute('name');

                        // Adicionar hidden input ao form
                        inp.parentNode.appendChild(hiddenInput);
                    });
                });

                // Se já tem valor (old input), formatar
                if (input.value) {
                    const numValue = parseFloat(input.value);
                    if (!isNaN(numValue)) {
                        input.value = 'R$ ' + numValue.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }
                }
            });
        });
    </script>
</body>

</html>
